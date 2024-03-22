<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Filter;

use Interfaces\Validator\Filter\ValidatorInterface;
use Models\AbstractProjectModels\Validation\Data\AbstractFactoryValidator;
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
        $actionType = strtolower($actionType);
        $className = 'Validator';
        $nameSpace = self::NAME_SPACE . 'Filter' . '\\';
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
            case 'head_admin' :
                $nameSpace .= 'HeadAdmin\\';
                break;
            default :
                break;
        }

        switch ($actionType) {
            case 'set':
                $nameSpace .= 'Set';
                break;
            case 'clear':
                $nameSpace .= 'Clear';
                break;
            default:
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);
        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
