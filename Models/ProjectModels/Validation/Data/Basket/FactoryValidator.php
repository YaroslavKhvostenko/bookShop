<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Basket;

use Interfaces\Validator\Basket\ValidatorInterface;
use Models\AbstractProjectModels\Validation\Data\AbstractFactoryValidator;

class FactoryValidator extends AbstractFactoryValidator
{
    /**
     * @param string $customerType
     * @param string $actionType
     * @return ValidatorInterface
     * @throws \Exception
     */
    public static function getValidator(string $customerType, string $actionType): ValidatorInterface
    {
        $customerType = strtolower($customerType);
        $className = 'Validator';
        $nameSpace = self::NAME_SPACE . 'Basket' . '\\';
        switch ($customerType) {
            case 'guest' :
                $nameSpace .= 'Guest\\';
                break;
            case 'user' :
                $nameSpace .= 'User\\';
                break;
            default :
                break;
        }

        $actionType = strtolower($actionType);
        switch ($actionType) {
            case 'add':
                $nameSpace .= 'Add';
                break;
            case 'update':
                $nameSpace .= 'Update';
                break;
            case 'remove':
                $nameSpace .= 'Remove';
                break;
            default:
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);
        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
