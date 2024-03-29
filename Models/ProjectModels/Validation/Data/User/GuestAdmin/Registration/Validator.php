<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\GuestAdmin\Registration;

use Models\AbstractProjectModels\Validation\Data\User\Guest\Registration\AbstractValidator;
use Models\ProjectModels\DataRegistry;

class Validator extends AbstractValidator
{
    private string $adminPass;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->adminPass = DataRegistry::getInstance()->get('config')->getAdminPass();
    }

    /**
     * @param string $field
     * @param string $value
     * @return bool|null
     * @throws \Exception
     */
    protected function checkEmptySpecific(string $field, string $value): ?bool
    {
        $result = $this->emptyCheckResult();
        switch ($field) {
            case 'admin_pass' :
                if ($value) {
                    $result = true;
                }

                return $result;
            default:
                return parent::checkEmptySpecific($field, $value);
        }
    }

    protected function emptyCheckResult(): ?bool
    {
        return false;
    }

    /**
     * @param string $field
     * @param string $value
     * @return string|null
     * @throws \Exception
     */
    protected function checkSpecificFields(string $field, string $value): ?string
    {
        $result = '';
        switch ($field) {
            case 'admin_pass' :
                if (password_verify($value, $this->adminPass)) {
                    $result = null;
                }

                return $result;
            default :
                throw new \Exception(
                'Unknown field :' . "'$field'" . 'during registration correctCheck data validation!'
            );
        }
    }
}
