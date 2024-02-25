<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User\Guest\Authorization;

use Models\AbstractProjectModels\Validation\Data\User\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
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
                    $result = $this->checkEmptySpecific($field, $value);
                    if ($result === true) {
                        $resultData[$field] = $value;
                    } else {
                        $resultData[$field] = false;
                    }
            }
        }

        return $resultData;
    }

    abstract protected function checkEmptySpecific(string $field, string $value): ?bool;
}
