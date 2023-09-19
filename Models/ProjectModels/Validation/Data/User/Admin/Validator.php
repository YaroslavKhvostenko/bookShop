<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Admin;

use \Models\AbstractProjectModels\Validation\Data\User\Validator as BaseValidator;
use Models\ProjectModels\DataRegistry;

class Validator extends BaseValidator
{
    private string $adminPass;

    /**
     * Validator constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->adminPass = DataRegistry::getInstance()->get('config')->getAdminPass();
    }

    /**
     * @param string $field
     * @param string $value
     * @return string
     * @throws \Exception
     */
    protected function specificFieldsCorrectCheck(string $field, string $value): string
    {
        $result = parent::checkResult();
        switch ($field) {
            case 'admin_pass' :
                if (!password_verify($value, $this->adminPass)) {
                    $result = '';
                }

                return $result;
            default:
                return parent::specificFieldsCorrectCheck($field, $value);
        }
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
            case 'admin_pass' :
                if ($value) {
                    $result = $value;
                }

                return $result;
            default:
                return parent::specificFieldsEmptyCheck($field, $value);
        }
    }

    protected function checkResult(): ?string
    {
        return null;
    }
}
