<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User;

use Models\AbstractProjectModels\Validation\Data\User\Validator as BaseValidator;

class Validator extends BaseValidator
{
    /**
     * @param array $data
     * @return array
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

    protected function lastFieldsCheck(string $field, string $value): ?string
    {
        switch ($field) {
            case 'phone' :
                if (strlen($value) !== 0 && $value !== '+380') {
                    return $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                }
                break;
            case 'address' :
                if (strlen($value) !== 0) {
                    return $this->pregMatchStrLen('/.{10,100}/u', $value);
                }
                break;
            default : return '';
        }
    }
}
