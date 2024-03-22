<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User;

use Interfaces\Validator\User\ValidatorInterface;
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
        $nameSpace = self::NAME_SPACE . 'User' . '\\';

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
            case 'guest_admin' :
                $nameSpace .= 'GuestAdmin\\';
                break;
            default :
                break;
        }


        switch ($actionType) {
            case 'add':
                $nameSpace .= 'Add';
                break;
            case 'authorization':
                $nameSpace .= 'Authorization';
                break;
            case 'change':
                $nameSpace .= 'Change';
                break;
            case 'registration':
                $nameSpace .= 'Registration';
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
