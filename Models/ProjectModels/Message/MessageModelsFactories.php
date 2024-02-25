<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message;

use Models\AbstractProjectModels\AbstractFactory;
use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;

class MessageModelsFactories extends AbstractFactory
{
    public static function getMessageModelsFactory(string $controller): AbstractMsgModelsFactory
    {
        $controller = strtolower($controller);
        $className = 'MsgModelsFactory';
        $nameSpace = 'Models\ProjectModels\Message\\';

        switch ($controller) {
            case 'admin_controller' :
                $nameSpace .= 'Admin';
                break;
            case 'book_controller' :
                $nameSpace .= 'Book';
                break;
            case 'catalog_controller' :
                $nameSpace .= 'Catalog';
                break;
            case 'user_controller' :
                $nameSpace .= 'User';
                break;
            case 'base_controller' :
                $nameSpace = 'Base';
                break;
            default :
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);

        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
