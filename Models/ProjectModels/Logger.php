<?php

namespace Models\ProjectModels;

class Logger
{
    private static Logger $instance;
    private const FILE_PATH = 'logs/';
    private const ADMINS_PATH = 'admins/';
    private const TYPE_PDO = 'pdo';
    private const TYPE_REFLECTION = 'reflection';
    private const TYPE_DEFAULT = 'default';
    private const ACTIVITY = 'activity';
    private const TYPE_FILES = [
        self::TYPE_PDO => 'pdo_error.log',
        self::TYPE_REFLECTION => 'reflection_error.log',
        self::TYPE_DEFAULT => 'default_error.log',
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

    public function log(string $typeFile, string $msgData): void
    {
        $filePath = $typeFile === 'activity' ? self::FILE_PATH . self::ADMINS_PATH : self::FILE_PATH;
        $data = date('[H:i:s d-m-Y]--', time()) . "\n" . $msgData . "\n\n";
        file_put_contents(
            $filePath . self::TYPE_FILES[$typeFile],
            $data . PHP_EOL,
            FILE_APPEND
        );
    }

    public function exceptionLog(\Exception $exception, $msg = null): void
    {
        if ($exception instanceof \ReflectionException) {
            $type = self::TYPE_REFLECTION;
        } elseif ($exception instanceof \PDOException) {
            $type = self::TYPE_PDO;
        } else {
            $type = self::TYPE_DEFAULT;
        }

        if ($msg === null) {
            $msg = $exception->getMessage() . "\n" . $exception->getTraceAsString();
        } else {
            $msg .= "\n" . $exception->getTraceAsString();
        }

        $this->log($type, $msg);
    }
}
