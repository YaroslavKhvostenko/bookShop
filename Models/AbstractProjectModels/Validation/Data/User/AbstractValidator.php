<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\User;

use Interfaces\Validator\User\ValidatorInterface;
use Models\AbstractProjectModels\Validation\Data\AbstractBaseValidator;

abstract class AbstractValidator extends AbstractBaseValidator implements ValidatorInterface
{
    abstract public function emptyCheck(array $data): array;

    abstract public function correctCheck(array $data): array;
}