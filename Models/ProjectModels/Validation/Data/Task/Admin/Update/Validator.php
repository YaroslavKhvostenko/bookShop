<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Task\Admin\Update;

use Models\AbstractProjectModels\Validation\Data\Task\Update\AbstractValidator;

class Validator extends AbstractValidator
{
    protected function emptyValidation(array $data): ?array
    {
        return !empty($data) ? $data : null;
    }

    protected function getEmptyDataField(): string
    {
        return 'empty_task';
    }

    protected function getNotUniqueFieldName(): string
    {
        return 'task_status';
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function validateFieldNames(string $fieldName): void
    {
        switch ($fieldName) {
            case 'task_status' :
                break;
            default :
                parent::validateFieldNames($fieldName);
        }
    }

    /**
     * @param string $fieldValue
     * @param string|null $fieldName
     * @return int|null
     * @throws \Exception
     */
    protected function validateFieldValues(string $fieldValue, string $fieldName = null): ?int
    {
        if (is_int(parent::validateFieldValues($fieldValue)) && $fieldName === $this->getNotUniqueFieldName()) {
            if ($fieldValue !== '1') {
                throw new \Exception(
                    'You can update `task_status` only like \'1\' at the moment!' .
                    'You received : ' . '\'' . $fieldValue . '\' !'
                );
            }
        }

        return (int)$fieldValue;
    }
}
