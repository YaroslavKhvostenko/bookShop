<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Exception\Controllers;

abstract class AbstractExceptionModel extends AbstractBaseExceptionModel
{
    /**
     * @param \Exception $exception
     * @param string|null $controller
     * @param string|null $action
     * @param string|null $params
     */
    abstract protected function exceptionCatcher(
        \Exception $exception,
        string $controller = null,
        string $action = null,
        string $params = null
    ): void;
}
