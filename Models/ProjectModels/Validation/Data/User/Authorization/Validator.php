<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Authorization;

use Models\AbstractProjectModels\Validation\Data\User\Authorization\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @param string $field
     * @param string $value
     * @return bool|null
     * @throws \Exception
     */
    protected function checkEmptySpecific(string $field, string $value): ?bool
    {
        throw new \Exception(
            'Unknown field :' . "'$field'" . 'during authorization emptyCheck data validation!'
        );
    }

    /**
     * TEMPORARY PLUG !!!!
     * @param array $data
     * @return array
     */
    public function correctCheck(array $data): array
    {
        return $data;
    }
}
