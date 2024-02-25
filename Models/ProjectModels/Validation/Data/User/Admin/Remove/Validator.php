<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Admin\Remove;

use Models\AbstractProjectModels\Validation\Data\User\User\Remove\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @param string $fieldName
     * @return string|null
     * @throws \Exception
     */
    public function validateFieldName(string $fieldName): ?string
    {
        throw new \Exception('Admins can only delete images from their profiles! Check the code!');
    }
}
