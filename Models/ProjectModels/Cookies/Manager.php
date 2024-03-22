<?php
declare(strict_types=1);

namespace Models\ProjectModels\Cookies;

class Manager
{
    private static Manager $selfInstance;
    private ?array $data;

    private function __construct()
    {
        $this->data = $_COOKIE ?? null;
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    public static function getInstance(): Manager
    {
        return static::createSelf();
    }

    protected static function createSelf(): Manager
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }

    /**
     * @param string $cookieTitle
     * @param string $item
     * @param int $cookieLiveTime
     * @throws \Exception
     */
    public function setData(string $cookieTitle, string $item , int $cookieLiveTime)
    {
        if ($this->validateCookieTitle($cookieTitle)) {
            setcookie(strtolower($cookieTitle), $item, time()+$cookieLiveTime, '/');
        } else {
            throw new \Exception(
                'Wrong cookie title during trying to initialize cookie'
            );
        }
    }

    /**
     * @param string $cookieTitle
     * @return string|null
     * @throws \Exception
     */
    public function getData(string $cookieTitle): ?string
    {
        if ($this->validateCookieTitle($cookieTitle)) {
            return $this->data[$cookieTitle] ?? null;
        } else {
            throw new \Exception(
                'Wrong cookie title during trying to get cookie data!'
            );
        }


    }

    /**
     * @param string $cookieTitle
     * @param string $item
     * @param int $cookieLiveTime
     * @throws \Exception
     */
    public function unsetData(string $cookieTitle, string $item , int $cookieLiveTime)
    {
        if ($this->validateCookieTitle($cookieTitle)) {
            setcookie(strtolower($cookieTitle), $item, time()-$cookieLiveTime, '/');
        } else {
            throw new \Exception(
                'Wrong cookie title during trying to initialize cookie'
            );
        }
    }

    private function validateCookieTitle(string $cookieTitle): bool
    {
        switch ($cookieTitle) {
            case 'basket' :
                $result = true;
                break;
            default:
                $result = false;
                break;
        }

        return $result;
    }
}
