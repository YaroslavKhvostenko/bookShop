<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User;

class Validator
{
    private const REG_FIELDS = [
        'login',
        'pass',
        'pass_confirm',
        'name',
        'birthdate',
        'email'
    ];

    private const LOG_FIELDS = [
        'login',
        'pass'
    ];

    private array $requiredFields = [
        'registration' => self::REG_FIELDS,
        'login' => self::LOG_FIELDS
    ];

    /**
     * @param array $data
     * @return array
     */
    public function emptyCheck(array $data, string $type): array
    {
        $resultData = [];
        foreach ($this->requiredFields[$type] as $field) {
            if (empty($data[$field])) {
                $resultData[$field] = false;
            } else {
                $resultData[$field] = $data[$field];
            }
        }
        return $resultData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function correctCheck(array $data): array // yarik
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
                    if (strlen($value) !== 0 && $value !== '+380') {
                        $resultData[$key] = $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                    }
                    break;
                case 'address' :
                    if (strlen($value) !== 0) {
                        $resultData[$key] = $this->pregMatchStrLen('/.{10,100}/u', $value);
                    }
                    break;
                default : $resultData[$key] = '';
            }
        }
        return $resultData;
    }

    /**
     * @param string $pattern
     * @param string $dataString
     * @return string
     */
    protected function pregMatchStrLen(string $pattern, string $dataString): string //yarik
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
    protected function severalLanguagesCheck(string $dataString): string //yarik
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
}
