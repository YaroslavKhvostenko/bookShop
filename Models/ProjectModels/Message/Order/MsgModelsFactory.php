<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Order;

use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
use Models\AbstractProjectModels\Message\Order\AbstractBaseMsgModel;
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
        $nameSpace = self::NAME_SPACE . 'Order' . self::isDefault($customerType);
        switch ($customerType) {
            case 'guest' :
                $nameSpace .= 'Guest\\';
                break;
            case 'user' :
                $nameSpace .= 'User\\';
                break;
            case 'admin' :
                $nameSpace .= 'Admin\\';
                break;
            case 'head_admin' :
                $nameSpace .= 'HeadAdmin\\';
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
                case 'create':
                    $nameSpace .= 'Create';
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
