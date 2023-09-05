<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Admin;

use \Models\AbstractProjectModels\Validation\Data\User\Validator as BaseValidator;
use Models\ProjectModels\DataRegistry;

class Validator extends BaseValidator
{
    private string $adminPass;
    private const REGISTRATION_FIELDS = [
        'phone',
        'address',
        'admin_pass'
    ];
    private const LOGIN_FIELDS = [
        'admin_pass'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->requiredFields['registration'] = array_merge(
            $this->requiredFields['registration'],
            self::REGISTRATION_FIELDS
        );
        $this->requiredFields['login'] = array_merge($this->requiredFields['login'], self::LOGIN_FIELDS);
        $this->adminPass = DataRegistry::getInstance()->get('config')->getAdminPass();
    }

    protected function emptyCheckCondition(string $data): bool
    {
        return (parent::emptyCheckCondition($data) || $data === '+380');
    }

    protected function lastFieldsCheck(string $field, string $value)
    {
        switch ($field) {
            case 'phone' :
                return $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
            case 'address' :
                return $this->pregMatchStrLen('/.{10,100}/u', $value);
            case 'admin_pass' :
                if (!password_verify($value, $this->adminPass)) {
                    return '';
                }
                break;
            default : return '';
        }
    }

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
                    $resultData[$key] = $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                    break;
                case 'address' :
                    $resultData[$key] = $this->pregMatchStrLen('/.{10,100}/u', $value);
                    break;
                case 'admin_pass' :
                    if (!password_verify($value, $this->adminPass)) {
                        $resultData[$key] = '';
                    }
                    break;
                default : $resultData[$key] = '';
            }
        }
        return $resultData;
    }
}
