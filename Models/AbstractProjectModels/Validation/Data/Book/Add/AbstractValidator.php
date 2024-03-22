<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Book\Add;

use Models\AbstractProjectModels\Validation\Data\Book\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
{
    abstract public function emptyCheck(array $data): array;

    abstract public function correctCheck(array $data): array;
}
