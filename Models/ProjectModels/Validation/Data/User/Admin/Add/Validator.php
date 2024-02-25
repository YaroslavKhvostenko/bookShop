<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Admin\Add;

use Models\AbstractProjectModels\Validation\Data\User\User\Add\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * TEMPORARY PLUG
     * @param array $data
     * @return array
     */
    public function emptyCheck(array $data): array
    {
        return $data;
    }

    /**
     * TEMPORARY PLUG
     * @param array $data
     * @return array
     */
    public function correctCheck(array $data): array
    {
        return $data;
    }

    /**
     * @param string $fieldName
     * @return string|null
     * @throws \Exception
     */
    public function validateFieldName(string $fieldName): ?string
    {
        throw new \Exception('Admins can add only images to their profiles! Check the code!');
    }

    /**
     * @param string $fieldName
     * @param array|null $data
     * @throws \Exception
     */
    public function compareFieldNames(string $fieldName, array $data = null): void
    {
        $this->validateFieldName($fieldName);
    }
}
