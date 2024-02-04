<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Remove;

use Models\AbstractProjectModels\Validation\Data\User\Remove\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @param string $fieldName
     * @return string|null
     * @throws \Exception
     */
    public function validateFieldName(string $fieldName): ?string
    {
        switch ($fieldName) {
            case 'address' :
            case 'phone' :
                return $fieldName;
            default:
                throw new \Exception(
                    'Unknown field :' . "'$fieldName'" . 'from URI string,
                     during Remove validation in validateFieldName method!'
                );
        }
    }
}
