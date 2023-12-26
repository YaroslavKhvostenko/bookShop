<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Exception\Controllers;

use Models\AbstractProjectModels\Exception\AbstractBaseExceptionModel as BaseExceptionControllerModel;

abstract class AbstractBaseExceptionModel extends BaseExceptionControllerModel
{
    /**
     * @param \Exception $exception
     * @param string|null $msg
     * @param string|null $firstUnusedParam
     * @param string|null $secondUnusedParam
     */
    abstract protected function exceptionCatcher(
        \Exception $exception,
        string $msg = null,
        string $firstUnusedParam = null,
        string $secondUnusedParam = null
    ): void;
}
