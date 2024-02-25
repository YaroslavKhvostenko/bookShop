<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\User\Remove;

use Interfaces\Validator\User\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    abstract public function validateFieldName(string $fieldName): ?string;
}
