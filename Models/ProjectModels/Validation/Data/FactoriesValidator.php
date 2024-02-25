<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data;

use Models\AbstractProjectModels\AbstractFactory;
use Models\AbstractProjectModels\Validation\Data\AbstractFactoryValidator;

class FactoriesValidator extends AbstractFactory
{
    public static function getFactoryValidator(string $controller): AbstractFactoryValidator
    {
        $controller = strtolower($controller);
        $className = 'FactoryValidator';
        $nameSpace = 'Models\ProjectModels\Validation\Data\\';

        switch ($controller) {
            case 'admin_controller' :
                $nameSpace .= 'Admin';
                break;
            case 'book_controller' :
                $nameSpace .= 'Book';
                break;
            case 'catalog_catalog' :
                $nameSpace .= 'Catalog';
                break;
            case 'user_controller' :
                $nameSpace .= 'User';
                break;
            default :
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);

        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
