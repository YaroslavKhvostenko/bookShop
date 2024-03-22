<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Guest\Registration;

use Models\AbstractProjectModels\Validation\Data\User\Guest\Registration\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @param string $field
     * @param string $value
     * @return string|null
     * @throws \Exception
     */
    protected function checkSpecificFields(string $field, string $value): ?string
    {
        throw new \Exception(
            'Unknown field :' . "'$field'" . 'during registration correctCheck data validation!'
        );
    }
}
