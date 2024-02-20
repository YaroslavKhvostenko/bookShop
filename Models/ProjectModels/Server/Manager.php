<?php
declare(strict_types=1);

namespace Models\ProjectModels\Server;

use Interfaces\IDataManagement;
//use mysql_xdevapi\Exception;

class Manager implements IDataManagement
{
    private array $data;
    private const REQUEST = 'request';
    private const REFERER = 'referer';
    private const USER_TYPE = 'user_type';
    private const ADMIN_TYPE = 'admin_type';
    private const CONTROLLER = 'controller';
    private const ACTION = 'action';
    private const USER = 'user';
    private const ADMIN = 'admin';
    private const HEAD = 'head';
    private array $serverOptions = [
        self::REQUEST => [],
        self::REFERER => []
    ];
    private const REQUEST_OPTIONS = [
        self::USER_TYPE => self::USER_TYPE,
        self::CONTROLLER => self::CONTROLLER,
        self::ACTION => self::ACTION,
        self::ADMIN_TYPE => self::ADMIN_TYPE
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

    public function getReferer(): string
    {
        return $this->data['HTTP_REFERER'];
    }

    /**
     * @param string $stringUriType
     * @throws \Exception
     */
    public function initializeServerUriOptions(string $stringUriType): void
    {
        if (!array_key_exists($stringUriType, $this->serverOptions)) {
            throw new \Exception('Wrong server URI type, check Server/Manager!');
        }

        if (empty($this->serverOptions[$stringUriType])) {
                $this->setServerUriOptions($stringUriType);
        }
    }

    private function setServerUriOptions(string $serverUriType): void
    {
        if ($serverUriType === self::REQUEST) {
            $result = $this->splitUriString($this->getRequestUri());
        } else {
            $result = $this->splitUriString(parse_url($this->getReferer(), PHP_URL_PATH));
        }

        $options = [];
        if (!isset($result[0])) {
            return;
        }

        if (strtolower($result[0]) === self::ADMIN) {
            if (strtolower($result[1]) === self::HEAD) {
                $options[self::USER_TYPE] = $result[0];
                $options[self::ADMIN_TYPE] = $result[1] . '_' . self::ADMIN;
                if (isset($result[2])) {
                    $options[self::CONTROLLER] = $result[2];
                }

                if (isset($result[3])) {
                    $options[self::ACTION] = $result[3];
                }
            } else {
                $options[self::USER_TYPE] = $result[0];
                $options[self::ADMIN_TYPE] = $result[0];
                if (isset($result[1])) {
                    $options[self::CONTROLLER] = $result[1];
                }

                if (isset($result[2])) {
                    $options[self::ACTION] = $result[2];
                }
            }
        } else {
            $options[self::USER_TYPE] = self::USER;
            $options[self::CONTROLLER] = $result[0];
            if (isset($result[1])) {
                $options[self::ACTION] = $result[1];
            }
        }

        $this->serverOptions[$serverUriType] = $this->lowerCase($options);
    }

    /**
     * @param string $requestOption
     * @return string
     * @throws \Exception
     */
    public function getRequestOption(string $requestOption): string
    {
        if (!array_key_exists($requestOption, self::REQUEST_OPTIONS) ||
            !array_key_exists($requestOption, self::REFERER_OPTIONS)) {
            throw new \Exception('Wrong name of server option: ' . "'$requestOption'!");
        }

        if (!array_key_exists($requestOption, $this->serverOptions[self::REQUEST])) {
            $this->initializeServerUriOptions(self::REQUEST);
        }

        return $this->serverOptions[self::REQUEST][strtolower($requestOption)];
    }

    /**
     * @param string $refererOption
     * @return string
     * @throws \Exception
     */
    public function getRefererOption(string $refererOption): string
    {
        if (!array_key_exists($refererOption, $this->serverOptions[self::REFERER])) {
            $this->initializeServerUriOptions(self::REFERER);
        }

        return $this->serverOptions[self::REFERER][strtolower($refererOption)];
    }

    private function splitUriString(string $string): array
    {
        return explode('/', trim($string, '/'));
    }

    private function lowerCase(array $data): array
    {
        foreach ($data as $field => $value) {
            $data[$field] = strtolower($value);
        }

        return $data;
    }
}
