<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\Authorization;

use Models\AbstractProjectModels\Validation\Data\User\AbstractUserValidator;

abstract class AbstractValidator extends AbstractUserValidator
{
    /**
     * @param array $data
     * @return array
     */
    public function emptyCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'login' :
                case 'pass' :
                    $resultData[$field] = !$value ? false : $value;
                    break;
                default:
                    $result = $this->specificFieldsEmptyCheck($field, $value);
                    if ($result === true) {
                        $resultData[$field] = $value;
                    } else {
                        $resultData[$field] = false;
                    }
            }
        }

        return $resultData;
    }

    abstract protected function specificFieldsEmptyCheck(string $field, string $value): ?bool;
}
