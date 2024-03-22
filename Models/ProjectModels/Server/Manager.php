<?php
declare(strict_types=1);

namespace Models\ProjectModels\Server;

use http\Exception\InvalidArgumentException;
use Interfaces\IDataManagement;
//use mysql_xdevapi\Exception;

class Manager implements IDataManagement
{
    private array $data;
    private array $serverOptions = [
        'request' => [],
        'referer' => []
    ];
    private const ADMINS_AREA = [
        'admin',
        'head'
    ];
    private const SERVER_STRINGS = [
        'request',
        'referer'
    ];
    private const SERVER_OPTIONS = [
        'request' => self::REQUEST_OPTIONS,
        'referer' => self::REFERER_OPTIONS
    ];
    private const REQUEST_OPTIONS = [
        'controller',
        'action',
        'params'
    ];
    private const REFERER_OPTIONS = self::REQUEST_OPTIONS;

    public function __construct()
    {
        $this->data = $_SERVER;
    }

    public function getRequestUri(): string
    {
        return $this->data['REQUEST_URI'];
    }

    public function getRefererUri(): string
    {
        return $this->data['HTTP_REFERER'];
    }

    /**
     * @param string $uriType
     * @param string $option
     * @return string
     * @throws \Exception
     */
    private function getOption(string $uriType, string $option): string
    {
        if (!in_array($uriType, self::SERVER_STRINGS)) {
            throw new InvalidArgumentException(
                'Undefined server type string : ' . '\'' . $uriType . '\'' .
                ', please check the correctness of server type string title, ' .
                'or does it exist in const SERVER_STRINGS!'
            );
        }

        if (!in_array($option, self::SERVER_OPTIONS[$uriType])) {
            throw new InvalidArgumentException(
                'Undefined server option title : ' . '\'' . $option . '\'' .
                ', please check the correctness of server option title, ' .
                'or does it exist in const SERVER_OPTIONS[' . '\'' . $uriType . '\'' . ']'
            );
        }

        if (empty($this->serverOptions[$uriType])) {
            $this->initializeData($uriType);
        }

        if ($this->serverOptions[$uriType] === 'action' && is_null($this->serverOptions[$uriType]['action'])) {
            throw new \Exception(
                'You can\'t use action option from ' . $uriType . ' string, ' .
                'because it doesn\'t exist in provided ' . $uriType . ' string! ' .
                'Probably it was IndexAction!'
            );
        }

        return $this->serverOptions[$uriType][$option];
    }

    /**
     * @param string $uriType
     * @throws \Exception
     */
    private function initializeData(string $uriType): void
    {
        if ($uriType === 'referer') {
            $uriString = parse_url($this->getRefererUri(), PHP_URL_PATH);
        } else {
            $uriString = $this->getRequestUri();
        }

        $result = $this->splitString($uriString);
        if (empty($result[0]) || (in_array($result[0], self::ADMINS_AREA) && !isset($result[1]))) {
            throw new \Exception(
                'You can\'t use options data from server ' . $uriType . ' string, ' .
                'because server ' . $uriType . ' string is empty!'
            );
        }

        if (in_array($result[0], self::ADMINS_AREA)) {
            array_shift($result);
        }

        $options['controller'] = array_shift($result);
        $options['action'] = isset($result[0]) ? array_shift($result) : null;
        $options['params'] = $result;
        $this->serverOptions[$uriType] = $this->lowerCase($options);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getRequestController(): string
    {
        return $this->getRequestOption('controller');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getRequestAction(): string
    {
        return $this->getRequestOption('action');
    }

    public function getRequestParams(): array
    {
        if (empty($this->serverOptions['request'])) {
            $this->initializeData('request');
        }

        return $this->serverOptions['request']['params'];
    }

    /**
     * @param string $option
     * @return string
     * @throws \Exception
     */
    private function getRequestOption(string $option): string
    {
        return $this->getOption('request', $option);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getRefererController(): string
    {
        return $this->getRefererOption('controller');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getRefererAction(): string
    {
        return $this->getRefererOption('action');
    }

    /**
     * @param string $option
     * @return string
     * @throws \Exception
     */
    private function getRefererOption(string $option): string
    {
        return $this->getOption('referer', $option);
    }

    public function getRefererParams(): array
    {
        if (empty($this->serverOptions['referer'])) {
            $this->initializeData('referer');
        }

        return $this->serverOptions['referer']['params'];
    }

    /**
     * @param string $uriString
     * @return array
     */
    public function splitString(string $uriString): array
    {
        $splits = explode('/', trim($uriString, '/'));
        $result = [];
        foreach ($splits as $uriParam) {
            if (!empty($uriParam)) {
                preg_match('/[a-z0-9]{1,16}/u', $uriParam, $matches);
                $result[] = $matches[0];
            } else {
                $result[] = trim($uriParam);
            }

        }

        return $result;
    }

    private function lowerCase(array $data): array
    {
        foreach ($data as $field => $value) {
            if (!is_array($value)) {
                $data[$field] = !is_null($value) ? strtolower($value) : $value;
            } else {
                foreach ($value as $indexKey => $paramData) {
                    $data[$field][$indexKey] = is_numeric($paramData) ? (int)$paramData : strtolower($paramData);
                }
            }
        }

        return $data;
    }

    public function isAdminArea(string $area): bool
    {
        return in_array($area, self::ADMINS_AREA);
    }
}
