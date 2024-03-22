<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Basket;

use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
use Models\AbstractProjectModels\Message\Basket\AbstractBaseMsgModel;
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
        $nameSpace = self::NAME_SPACE . 'Basket' . self::isDefault($customerType);
        switch ($customerType) {
            case 'guest' :
                $nameSpace .= 'Guest\\';
                break;
            case 'user' :
                $nameSpace .= 'User\\';
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
                case 'clear':
                    $nameSpace .= 'Clear';
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
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);
        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
