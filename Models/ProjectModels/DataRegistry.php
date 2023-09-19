<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Interfaces\IDataManagement;

class DataRegistry
{
    private static DataRegistry $instance;
    private array $registry;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance(): DataRegistry
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * @param string $key
     * @param IDataManagement $object
     * @return $this
     * @throws \Exception
     */
    public function register(string $key, IDataManagement $object): DataRegistry
    {
        if (!isset($this->registry[$key])) {
            $this->registry[$key] = $object;
        } else {
            throw new \Exception('Item with the same key already exists.');
        }

        return $this;
    }

    /**
     * @param string $key
     * @return IDataManagement
     * @throws \Exception
     */
    public function get(string $key): IDataManagement
    {
        if (!isset($this->registry[$key])) {
            throw new \Exception('Item with key: ' . $key . ' not found ');
        } else {
            return $this->registry[$key];
        }
    }
}
