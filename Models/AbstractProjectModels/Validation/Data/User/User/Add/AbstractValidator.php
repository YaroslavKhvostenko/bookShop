<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\User\Add;

use Models\AbstractProjectModels\Validation\Data\User\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
{
    abstract public function validateFieldName(string $fieldName): ?string;

    abstract public function compareFieldNames(string $fieldName, array $data = null): void;
}
