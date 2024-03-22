<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Book;

use Interfaces\Validator\Book\ValidatorInterface;
use Models\AbstractProjectModels\Validation\Data\AbstractFactoryValidator;
use Models\ProjectModels\Validation\Data\Book\Admin;
//use mysql_xdevapi\Exception;

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
        $nameSpace = self::NAME_SPACE . 'Book' . '\\';
        switch ($customerType) {
            case 'admin' :
                $nameSpace .= 'Admin\\';
                break;
            case 'user' :
                $nameSpace .= 'User\\';
                break;
            case 'guest' :
                $nameSpace .= 'Guest\\';
                break;
            default :
                break;
        }

        $actionType = strtolower($actionType);
        switch ($actionType) {
            case 'add':
                $nameSpace .= 'Add';
                break;
            case 'show':
                $nameSpace .= 'Show';
                break;
            case 'update' :
                $nameSpace .= 'Update';
                break;
            default:
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);
        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
