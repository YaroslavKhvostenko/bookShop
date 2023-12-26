<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Exception\Models;

use Models\AbstractProjectModels\Exception\AbstractBaseExceptionModel;

abstract class AbstractExceptionModel extends AbstractBaseExceptionModel
{
    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    protected function exceptionCatcher(\Exception $exception): void
    {
        $this->getLogger()->exceptionLog($exception);
    }
}
