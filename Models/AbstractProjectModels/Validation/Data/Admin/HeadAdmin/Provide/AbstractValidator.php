<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Admin\HeadAdmin\Provide;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Validation\Data\Admin\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
{
    protected array $fieldNames = [];
    protected array $fieldValues = [];
    protected string $fieldName;
    protected string $fieldValue;

    public function emptyCheckBox(array $data): array
    {
        if (empty($data)) {
            $data['empty_data'] = false;
        }

        return $data;
    }

    /**
     * @param array $data
     * @param string|null $actionName
     * @return array
     * @throws \Exception
     */
    public function correctCheckBox(array $data, string $actionName = null): array
    {
        $result = [];
        foreach ($data as $adminLogin => $adminData) {
            $result[$adminLogin] = $this->validateCheckBoxData($this->splitString($adminData), $actionName);
        }

        return $result;
    }

    /**
     * @param string $string
     * @return array|null
     * @throws \Exception
     */
    protected function splitString(string $string): ?array
    {
        $splits = array_pad(explode('/', trim($string, '/')), 3, '');
        if (count($splits) > 3) {
            throw new \Exception(
                'You wrote extra data to CheckBox value string! Check CheckBox form!'
            );
        }
        $result = [
            'id_value' => $splits[0] !== '' ? (int)$splits[0] : '',
            'field_name' => $splits[1],
            'field_value' => $splits[2]
        ];

        if (in_array('', $result)) {
            throw new \Exception(
                'You missed some data from CheckBox value string, during splitting string! Check CheckBox form!'
            );
        }

        return $result;
    }

    protected function validateCheckBoxData(array $data, string $actionName): array
    {
        $result['id'] = $data['id_value'];
        $fieldName = $this->validateFieldName($data['field_name'], $actionName);

        $fieldValue = $this->validateFieldValue($data['field_value'], $actionName);
        if ($fieldValue && $fieldName) {
            $result[$this->fieldName] = (int)$this->fieldValue;
        }

        return $result;
    }

    protected function validateFieldName(string $fieldName, string $actionName): bool
    {
        if ($fieldName === $this->fieldNames[$actionName]) {
            $this->fieldName = $fieldName;

            return true;
        }

        throw new InvalidArgumentException(
            'Wrong $fieldName : ' . "'$fieldName'" . ', during validateFieldName!'
        );
    }

    protected function validateFieldValue(string $fieldValue, string $actionName): bool
    {
        if ($fieldValue === $this->fieldValues[$actionName]) {
            $this->fieldValue = $fieldValue;

            return true;
        }

        throw new InvalidArgumentException(
            'Wrong $fieldValue : ' . "'$fieldValue'" . ', during validateFieldValue!'
        );
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
