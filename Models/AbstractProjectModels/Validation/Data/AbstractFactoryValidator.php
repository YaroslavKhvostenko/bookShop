<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data;

use Interfaces\Validator\DataValidatorInterface;
use Models\AbstractProjectModels\AbstractFactory;

abstract class AbstractFactoryValidator extends AbstractFactory
{
    protected const NAME_SPACE = 'Models\ProjectModels\Validation\Data\\';

    public function __construct()
    {

    }

    abstract public static function getValidator(string $customerType, string $actionType): DataValidatorInterface;
}
