<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\GuestAdmin\Authorization;

use Models\AbstractProjectModels\Validation\Data\User\Guest\Authorization\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @param string $field
     * @param string $value
     * @return bool|null
     * @throws \Exception
     */
    protected function checkEmptySpecific(string $field, string $value): ?bool
    {
        $result = false;
        switch ($field) {
            case 'admin_pass' :
                if ($value) {
                    $result = true;
                }

                return $result;
            default:
                throw new \Exception(
                    'Unknown field :' . "'$field'" . 'during authorization emptyCheck data validation!'
                );
        }
    }

    /**
     * ЗАТЫЧКА !!!!
     * @param array $data
     * @return array
     */
    public function correctCheck(array $data): array
    {
        return $data;
    }
}
