<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Filter\Clear;
use Models\AbstractProjectModels\Validation\Data\Filter\AbstractBaseValidator as BaseValidator;

abstract class AbstractBaseValidator extends BaseValidator
{
    public function emptyCheck(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        return true;
    }
}
