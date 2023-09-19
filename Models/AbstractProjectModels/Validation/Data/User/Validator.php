<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User;

use Interfaces\User\UserDataValidatorInterface;

abstract class Validator implements UserDataValidatorInterface
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
                    $result = $this->specificFieldsEmptyCheck($field, $value);
                    if ($result === null) {
                        $resultData[$field] = false;
                    } elseif ($result !== 'not_necessary') {
                        $resultData[$field] = $result;
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
    protected function specificFieldsEmptyCheck(string $field, string $value): ?string
    {
        $result = $this->checkResult();
        switch ($field) {
            case 'phone' :
                if ($value && $value !== '+380') {
                    $result = $value;
                }

                return $result;
            case 'address' :
                if ($value) {
                    $result = $value;
                }

                return $result;
            default:
                throw new \Exception('Field : \'' . $field . '\' doesn\'t exist!');
        }
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
                 default :
                     $result = $this->specificFieldsCorrectCheck($key, $value);
                     if ($result !== 'not_necessary') {
                         $resultData[$key] = $result;
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
    protected function specificFieldsCorrectCheck(string $field, string $value): ?string
    {
        switch ($field) {
            case 'phone' :
                return $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
            case 'address' :
                return $this->pregMatchStrLen('/.{10,100}/u', $value);
            default:
                throw new \Exception('Field : \'' . $field . '\' doesn\'t exist!');
        }
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

    protected function checkResult(): ?string
    {
        return 'not_necessary';
    }
}
