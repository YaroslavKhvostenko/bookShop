<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Exception\DbModels;

use Models\AbstractProjectModels\Exception\AbstractBaseExceptionModel;

abstract class AbstractExceptionModel extends AbstractBaseExceptionModel
{
     abstract protected function exceptionCatcher(\PDOException $exception, string $msg = null): void;
}
