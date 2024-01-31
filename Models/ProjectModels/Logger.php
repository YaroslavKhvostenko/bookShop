<?php

namespace Models\ProjectModels;

use Models\ProjectModels\Exception;

class Logger
{
    private static Logger $instance;
    private const FILE_PATH = 'logs/';
    private const ADMINS_PATH = 'admins/';
    private const PDO = 'pdo';
    private const REFLECTION = 'reflection';
    private const DEFAULT = 'default';
    private const ACTIVITY = 'activity';
    private const LOG_FILES = [
        self::PDO => 'pdo_error.log',
        self::REFLECTION => 'reflection_error.log',
        self::DEFAULT => 'default_error.log',
        self::ACTIVITY => 'admins_activity.log'
    ];

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    public static function getInstance(): Logger
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function log(string $fileType, string $msgData): void
    {
        $filePath = $fileType === 'activity' ? self::FILE_PATH . self::ADMINS_PATH : self::FILE_PATH;
        $data = date('[H:i:s d-m-Y]--', time()) . "\n" . $msgData . "\n\n";
        file_put_contents(
            $filePath . self::LOG_FILES[$fileType],
            $data . PHP_EOL,
            FILE_APPEND
        );
    }

    public function logException(\Exception $exception, string $msgData = null): void
    {
        $this->log(
            Exception\Manager::getTypeOfException($exception),
            Exception\Manager::createMsgOfException($exception, $msgData)
        );
    }
}
