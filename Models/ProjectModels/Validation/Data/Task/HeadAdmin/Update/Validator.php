<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Task\HeadAdmin\Update;

use Models\AbstractProjectModels\Validation\Data\Task\Update\AbstractValidator;

class Validator extends AbstractValidator
{
    protected function emptyValidation(array $data): ?array
    {
        $arrayUnique = array_unique($data);
        $countUnique = count($arrayUnique);
        if ($countUnique === 1 && $arrayUnique[0] === '') {
            $result = null;
        } else {
            foreach ($data as $field => $value) {
                if ($value !== '') {
                    $result[$field] = $value;
                }
            }
        }

        return $result;
    }

    protected function getEmptyDataField(): string
    {
        return 'empty_admin';
    }

    protected function getNotUniqueFieldName(): string
    {
        return 'admin_id';
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function validateFieldNames(string $fieldName): void
    {
        switch ($fieldName) {
            case 'admin_id' :
                break;
            default :
                parent::validateFieldNames($fieldName);
        }
    }

    protected function validateSpecificField(string $fieldName): bool
    {
        switch ($fieldName) {
            case 'admin_id' :
                return true;
            default :
                return parent::validateSpecificField($fieldName);
        }
    }

    protected function validateChangingField(string $fieldName = null): bool
    {
        switch ($fieldName) {
            case 'admin_id' :
                return true;
            default :
                return parent::validateChangingField();
        }
    }

    /**
     * @param string $fieldName
     * @return string|null
     * @throws \Exception
     */
    protected function getChangingField(string $fieldName): ?string
    {
        switch ($fieldName) {
            case 'admin_id' :
                return 'id';
            default :
                throw new \Exception(
                    'Unknown field name : ' . '\'' . $fieldName . '\'' .
                    ' during selecting changing field!' .
                    ' Probably comes wrong fieldName or you forgot to add it into switch() function!'
                );
        }
    }

    /**
     * @param string $fieldName
     * @return string[]|null
     * @throws \Exception
     */
    protected function getFieldForSelect(string $fieldName): ?array
    {
        switch ($fieldName) {
            case 'admin_id' :
                return ['id'];
            default :
                return parent::getFieldForSelect($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @return string[]|null
     * @throws \Exception
     */
    protected function getDbTable(string $fieldName): ?array
    {
        switch ($fieldName) {
            case 'admin_id' :
                return ['admins'];
            default :
                return parent::getDbTable($fieldName);
        }
    }
}
