<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\Guest\Registration;

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
                case 'name' :
                case 'birthdate' :
                case 'email' :
                    $resultData[$field] = !$value ? false : $value;
                    break;
                default:
                    $result = $this->checkEmptySpecific($field, $value);
                    if ($result === true) {
                        $resultData[$field] = $value;
                    } elseif ($result === false) {
                        $resultData[$field] = false;
                    } else {
                        break;
                    }
            }
        }

        return $resultData;
    }

    /**
     * @param string $field
     * @param string $value
     * @return string|null
     * @throws \Exception
     */
    protected function checkEmptySpecific(string $field, string $value): ?bool
    {
        $result = $this->emptyCheckResult();
        switch ($field) {
            case 'phone' :
                if ($value && $value !== '+380') {
                    $result = true;
                }

                return $result;
            case 'address' :
                if ($value) {
                    $result = true;
                }

                return $result;
            default:
                throw new \InvalidArgumentException(
                    'Field \'' . $field . '\' does not exist in the provided data array.'
                );
        }
    }

    protected function emptyCheckResult(): ?bool
    {
        return null;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function correctCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'login' :
                case 'pass' :
                    $resultData[$key] = $this->pregMatchStrLen('/[A-Za-z0-9]{4,16}/u', $value);
                    break;
                case 'pass_confirm' :
                    if ($value !== $data['pass']) {
                        $resultData[$key] = '';
                    }
                    break;
                case 'name' :
                    $resultData[$key] = $this->severalLanguagesCheck($value);
                    break;
                case 'birthdate' :
                    $resultData[$key] = $this->checkDate($value);
                    break;
                case 'email' :
                    $resultData[$key] = $this->checkEmail($value);
                    break;
                case 'phone' :
                    $resultData[$key] = $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                    break;
                case 'address' :
                    $resultData[$key] = $this->pregMatchStrLen('/.{10,100}/u', $value);
                    break;
                default :
                    $result = $this->checkSpecificFields($key, $value);
                    if ($result === '') {
                        $resultData[$key] = $result;
                    } else {
                        break;
                    }
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

    abstract protected function checkSpecificFields(string $field, string $value): ?string;
}
