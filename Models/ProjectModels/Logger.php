<?php

namespace Models\ProjectModels;

/**
 * @package Models\ProjectModels
 */
class Logger
{
    private const FILE_PATH = 'logs/';
    private const TYPE_PDO = 'pdo';
    private const TYPE_REFLECTION = 'reflection';
    private const TYPE_DEFAULT = 'default';
    private const TYPE_FILES = [
        self::TYPE_PDO => 'pdo_error.log',
        self::TYPE_REFLECTION => 'reflection_error.log',
        self::TYPE_DEFAULT => 'default_error.log'
    ];

    /**
     * @param string $typeFile
     * @param string $msgData
     */
    public function log(string $typeFile, string $msgData): void
    {
        $data = date('[H:i:s d-m-Y]--', time()) . "\n" . $msgData . "\n\n";
        file_put_contents(self::FILE_PATH . self::TYPE_FILES[$typeFile],
            $data . PHP_EOL, FILE_APPEND);
    }
}
