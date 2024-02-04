<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\Add;

use Models\AbstractProjectModels\Validation\Data\User\AbstractUserValidator;

abstract class AbstractValidator extends AbstractUserValidator
{
    abstract public function validateFieldName(string $fieldName): ?string;

    abstract public function compareFieldNames(string $fieldName, array $data = null): void;
}
