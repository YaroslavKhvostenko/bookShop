<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Book;

use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
use Models\AbstractProjectModels\Message\Book\AbstractBaseMsgModel;
//use mysql_xdevapi\Exception;

class MsgModelsFactory extends AbstractMsgModelsFactory
{
    /**
     * @param string $customerType
     * @param string|null $actionType
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    public static function getMsgModel(string $customerType, string $actionType = null): AbstractBaseMsgModel
    {
        $customerType = strtolower($customerType);
        $className = 'MsgModel';
        $nameSpace = self::NAME_SPACE . 'Book' . self::isDefault($customerType);
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
            case 'default' :
                $className = 'DefaultMsgModel';
                break;
            default :
                break;
        }

        if (!is_null($actionType)) {
            $actionType = strtolower($actionType);
            switch ($actionType) {
                case 'add':
                    $nameSpace .= 'Add';
                    break;
                case 'show':
                    $nameSpace .= 'Show';
                    break;
                case 'update':
                    $nameSpace .= 'Update';
                    break;
                default:
                    break;
            }
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);
        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
