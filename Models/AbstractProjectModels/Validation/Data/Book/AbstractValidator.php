<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Book;

use Interfaces\Book\BookDataValidatorInterface;
use Models\AbstractProjectModels\Validation\Data\AbstractBaseValidator;

abstract class AbstractValidator extends AbstractBaseValidator implements BookDataValidatorInterface
{

}