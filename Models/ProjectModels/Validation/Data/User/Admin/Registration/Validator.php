<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Admin\Registration;

use Models\AbstractProjectModels\Validation\Data\User\Registration\AbstractValidator;
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
    protected function specificFieldsEmptyCheck(string $field, string $value): ?bool
    {
        $result = $this->emptyCheckResult();
        switch ($field) {
            case 'admin_pass' :
                if ($value) {
                    $result = true;
                }

                return $result;
            default:
                return parent::specificFieldsEmptyCheck($field, $value);
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
    protected function specificFieldsCorrectCheck(string $field, string $value): ?string
    {
        $result = '';
        switch ($field) {
            case 'admin_pass' :
                if ($value === $this->adminPass) {
                    $result = null;
                }

                return $result;
            default : throw new \Exception(
                'Unknown field :' . "'$field'" . 'during registration correctCheck data validation!'
            );
        }
    }
}
