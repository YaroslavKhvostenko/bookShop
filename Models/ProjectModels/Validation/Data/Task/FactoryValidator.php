<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Task;

use Interfaces\Validator\Task\ValidatorInterface;
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
        $className = 'Validator';
        $nameSpace = self::NAME_SPACE . 'Task' . '\\';
        switch ($customerType) {
            case 'head_admin' :
                $nameSpace .= 'HeadAdmin\\';
                break;
            case 'admin' :
                $nameSpace .= 'Admin\\';
                break;
            default :
                break;
        }

        $actionType = strtolower($actionType);
        switch ($actionType) {
            case 'create':
                $nameSpace .= 'Create';
                break;
            case 'update':
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
