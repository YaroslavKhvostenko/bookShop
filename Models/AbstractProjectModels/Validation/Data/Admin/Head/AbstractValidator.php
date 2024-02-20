<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Admin\Head;

use http\Exception\InvalidArgumentException;
use Interfaces\Admin\AdminDataValidatorInterface;

abstract class AbstractValidator implements AdminDataValidatorInterface
{
    private const FIELD_NAMES = [];
    private const FIELD_VALUES = [];
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

    public function correctCheckBox(array $data, string $actionName = null): array
    {
        $result = [];
        foreach ($data as $adminLogin => $adminData) {
            $result[$adminLogin] = $this->validateCheckBoxData($this->splitString($adminData), $actionName);
        }

        return $result;
//        return $this->validateCheckBoxData($this->splitString($data['change']), $actionName);
    }

    protected function splitString(string $string): ?array
    {
        $result = [];
        $splits = explode('/', trim($string, '/'));
        if (isset($splits[0])) {
            $result['id_value'] = (int)$splits[0];
        } else {
            $result['id_value'] = '';
        }

        if (isset($splits[1])) {
            $result['field_name'] = $splits[1];
        } else {
            $result['field_name'] = '';
        }

        if (isset($splits[2])) {
            $result['field_value'] = $splits[2];
        } else {
            $result['field_value'] = '';
        }

        if (in_array('', $result)) {
            throw new \Exception(
                'You missed some data from CheckBox value string,
                during splitting string! Check CheckBox form!'
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
