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
    private const CONTROLLER = 'controller';
    private const ACTION = 'action';
    private const ADMIN = 'admin';
    private const USER = 'user';
    private array $serverOptions = [
        self::REQUEST => [],
        self::REFERER => []
    ];

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
                $this->serverUriOptionsSetter($stringUriType);
        }
    }

    private function serverUriOptionsSetter(string $serverUriType): void
    {
        if ($serverUriType === self::REQUEST) {
            $result = $this->uriStringSplitter($this->getRequestUri());
        } else {
            $result = $this->uriStringSplitter(parse_url($this->getReferer(), PHP_URL_PATH));
        }

        $options = [];
        if (strtolower($result[0]) === self::ADMIN) {
            $options[self::USER_TYPE] = $result[0];
            $options[self::CONTROLLER] = $result[1];
            $options[self::ACTION] = $result[2];
        } else {
            $options[self::USER_TYPE] = self::USER;
            $options[self::CONTROLLER] = $result[0];
            $options[self::ACTION] = $result[1];
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

    private function uriStringSplitter(string $uriString): array
    {
        return explode('/', trim($uriString, '/'));
    }

    private function lowerCase(array $data): array
    {
        foreach ($data as $field => $value) {
            $data[$field] = strtolower($value);
        }

        return $data;
    }
}
