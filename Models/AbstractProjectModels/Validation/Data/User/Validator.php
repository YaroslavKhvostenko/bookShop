<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User;

use Interfaces\User\UserDataValidatorInterface;

abstract class Validator implements UserDataValidatorInterface
{
    private const REGISTRATION_FIELDS = [
        'login',
        'pass',
        'pass_confirm',
        'name',
        'birthdate',
        'email'
    ];
    private const LOGIN_FIELDS = [
        'login',
        'pass'
    ];
    protected array $requiredFields = [];

    public function __construct()
    {
        $this->requiredFields = [
            'registration' => self::REGISTRATION_FIELDS,
            'login' => self::LOGIN_FIELDS
        ];
    }

    /**
     * @param array $data
     * @param string $type
     * @return array
     */
    public function emptyCheck(array $data, string $type): array
    {
        $resultData = [];
        foreach ($this->requiredFields[$type] as $field) {
            if ($this->emptyCheckCondition($data[$field])) {
                $resultData[$field] = false;
            } else {
                $resultData[$field] = $data[$field];
            }
        }
        return $resultData;
    }

    protected function emptyCheckCondition(string $data): bool
    {
        return empty($data);
    }


//    /**
//     * @param array $data
//     * @return array
//     */
//     public function correctCheck(array $data): array
//     {
//         $resultData = [];
//         foreach ($data as $key => $value) {
//             switch ($key) {
//                 case 'login' :
//                 case 'pass' :
//                     $resultData[$key] = $this->pregMatchStrLen('/[A-Za-z0-9]{4,16}/u', $value);
//                     break;
//                 case 'pass_confirm' :
//                     if ($value !== $data['pass']) {
//                         $resultData[$key] = '';
//                     }
//                     break;
//                 case 'name' :
//                     $resultData[$key] = $this->severalLanguagesCheck($value);
//                     break;
//                 case 'birthdate' :
//                     $resultData[$key] = $this->checkDate($value);
//                     break;
//                 case 'email' :
//                     $resultData[$key] = $this->checkEmail($value);
//                     break;
//                 default :
//                     $resultData[$key] = $this->lastFieldsCheck($key,$value);
//             }
//         }
//         return $resultData;
//     }

    /**
     * @param string $pattern
     * @param string $dataString
     * @return string
     */
    protected function pregMatchStrLen(string $pattern, string $dataString): string
    {
        preg_match($pattern, $dataString, $result);
        if (isset($result[0])) {
            return strlen($dataString) === strlen($result[0]) ? $result[0] : '';
        } else {
            return '';
        }
    }

    /**
     * @param string $dataString
     * @return string
     */
    protected function severalLanguagesCheck(string $dataString): string
    {
        if ($this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $dataString) === '') {
            return $this->pregMatchStrLen('/[A-Za-z]{2,30}/u', $dataString);
        } else {
            return $this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $dataString);
        }
    }

    /**
     * @param string $dateString
     * @return string
     */
    protected function checkDate(string $dateString): string
    {
        $arr = explode(".", $dateString);
        if (count($arr) !== 3) {
            return '';
        } else {
            return checkdate((int) $arr[1], (int) $arr[0], (int) $arr[2]) ? $dateString : '';
        }
    }

    /**
     * @param string $emailString
     * @return string
     */
    protected function checkEmail(string $emailString): string
    {
        if ($emailString !== '') {
            return filter_var($emailString, FILTER_VALIDATE_EMAIL) ? $emailString : '';
        } else {
            return '';
        }
    }

    /**
     * @param string $field
     * @param string $value
     * @return mixed
     */
    abstract protected function lastFieldsCheck(string $field, string $value);

    abstract public function correctCheck(array $data): array;
}
