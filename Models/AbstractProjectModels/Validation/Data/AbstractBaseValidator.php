<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data;

use Interfaces\User\UserDataValidatorInterface;

abstract class AbstractBaseValidator implements UserDataValidatorInterface
{
    abstract public function emptyCheck(array $data): array;

    abstract public function correctCheck(array $data): array;
}
