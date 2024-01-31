<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\Remove;

use Interfaces\User\UserDataValidatorInterface;

abstract class AbstractValidator implements UserDataValidatorInterface
{
    abstract public function validateFieldName(string $fieldName): ?string;
}
