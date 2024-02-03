<?php
declare(strict_types=1);

namespace Models\ProjectModels\Exception;

class Manager
{
    private const PDO = 'pdo';
    private const REFLECTION = 'reflection';
    private const DEFAULT = 'default';

    public static function getTypeOfException(\Exception $exception): string
    {
        if ($exception instanceof \ReflectionException) {
            $type = self::REFLECTION;
        } elseif ($exception instanceof \PDOException) {
            $type = self::PDO;
        } else {
            $type = self::DEFAULT;
        }

        return $type;
    }

    public static function createExceptionMessage(\Exception $exception, string $msg = null): string
    {
        if ($msg !== null) {
            $msg .= "\n";
        }

        $msg .= 'Error: ' . $exception->getMessage() . "\n" .
                'File: ' . $exception->getFile() . "\n" .
                'Line: ' . $exception->getLine() . "\n" .
                'TraceAsString: ' . $exception->getTraceAsString() . "\n";

        return $msg;
    }
}
