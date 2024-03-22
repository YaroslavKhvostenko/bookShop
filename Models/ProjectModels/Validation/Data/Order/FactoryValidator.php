<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Order;

use Interfaces\Validator\Order\ValidatorInterface;
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
        $nameSpace = self::NAME_SPACE . 'Order' . '\\';
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
            case 'create':
                $nameSpace .= 'Create';
                break;
            default:
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);
        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
