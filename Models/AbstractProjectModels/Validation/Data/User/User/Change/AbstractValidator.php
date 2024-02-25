<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\User\Change;

use Models\AbstractProjectModels\Validation\Data\User\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function emptyCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'login' :
                case 'pass' :
                case 'pass_confirm' :
                case 'old_pass' :
                case 'name' :
                case 'birthdate' :
                case 'email' :
                case 'address' :
                    $resultData[$field] = !$value ? false : $value;
                    break;
                case 'phone' :
                    $resultData[$field] = (!$value || $value === '+380') ? false : $value;
                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Field \'' . $field . '\' does not exist in the provided data array.'
                    );
            }
        }

        return $resultData;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function correctCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'login' :
                case 'old_pass':
                case 'pass' :
                    $resultData[$field] = $this->pregMatchStrLen('/[A-Za-z0-9]{4,16}/u', $value);
                    break;
                case 'pass_confirm' :
                    if ($value !== $data['pass']) {
                        $resultData[$field] = '';
                    }
                    break;
                case 'name' :
                    $resultData[$field] = $this->severalLanguagesCheck($value);
                    break;
                case 'birthdate' :
                    $resultData[$field] = $this->checkDate($value);
                    break;
                case 'email' :
                    $resultData[$field] = $this->checkEmail($value);
                    break;
                case 'phone' :
                    $resultData[$field] = $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                    break;
                case 'address' :
                    $resultData[$field] = $this->pregMatchStrLen('/.{10,100}/u', $value);
                    break;
                default :
                    throw new \InvalidArgumentException(
                        'Field \'' . $field . '\' does not exist in the provided data array.'
                    );
            }
        }

        return $resultData;
    }

    protected function pregMatchStrLen(string $pattern, string $dataString): string
    {
        preg_match($pattern, $dataString, $matches);
        $result = '';
        if (isset($matches[0])) {
            $result = strlen($dataString) === strlen($matches[0]) ? $matches[0] : $result;
        }

        return $result;
    }

    protected function severalLanguagesCheck(string $dataString): string
    {
        if ($this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $dataString) === '') {
            return $this->pregMatchStrLen('/[A-Za-z]{2,30}/u', $dataString);
        } else {
            return $this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $dataString);
        }
    }

    protected function checkDate(string $dateString): string
    {
        $result = '';
        $arr = explode(".", $dateString);
        if (count($arr) === 3 && checkdate((int) $arr[1], (int) $arr[0], (int) $arr[2])) {
            $result = $dateString;
        }

        return $result;
    }

    protected function checkEmail(string $emailString): string
    {
        return filter_var($emailString, FILTER_VALIDATE_EMAIL) ? $emailString : '';
    }

    /**
     * @param string $fieldName
     * @return string|null
     * @throws \Exception
     */
    public function validateFieldName(string $fieldName): ?string
    {
        switch ($fieldName) {
            case 'login' :
            case 'pass' :
            case 'name' :
            case 'birthdate' :
            case 'email' :
            case 'address' :
            case 'phone' :
                return $fieldName;
            default:
                throw new \Exception(
                    'Unknown field :' . "'$fieldName'" . 'from URI string,
                     during Change validation in validateFieldName method!'
                );
        }
    }

    /**
     * @param string $fieldName
     * @param array $data
     * @throws \Exception
     */
    public function compareFieldNames(string $fieldName, array $data): void
    {
        if ($this->validateFieldName($fieldName) === $fieldName) {
            if (!array_key_exists($fieldName, $data)) {
                throw new \InvalidArgumentException(
                    'Field \'' . $fieldName . '\' does not exist in the provided data array.'
                );
            }
        }
    }
}
