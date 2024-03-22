<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

abstract class AbstractFactory
{
    /**
     * @param string $classNameWithNamespace
     * @throws \Exception
     */
    protected static function isClassExist(string $classNameWithNamespace): void
    {
        if (!class_exists($classNameWithNamespace)) {
            throw new \Exception("Class '$classNameWithNamespace' doesn't exist!");
        }
    }

    protected static function createClassDirectoryPath(string $nameSpace, string $className): string
    {
        return $nameSpace . '\\' . $className;
    }
}
