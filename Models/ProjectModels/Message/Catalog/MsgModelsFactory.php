<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Catalog;

use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;
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
        $nameSpace = self::NAME_SPACE . 'Catalog' . self::isDefault($customerType);
        $className = 'MsgModel';

        switch ($customerType) {
            case 'head_admin':
                $nameSpace .= 'HeadAdmin\\';
                break;
            case 'admin':
                $nameSpace .= 'Admin\\';
                break;
            case 'user':
                $nameSpace .= 'User\\';
                break;
            case 'guest':
                $nameSpace .= 'Guest\\';
                break;
            case 'default' :
                $className = 'DefaultMsgModel';
                break;
            default:
                break;
        }

        if (!is_null($actionType)) {
            $actionType = strtolower($actionType);
            switch ($actionType) {
                case 'show':
                    $nameSpace .= 'Show';
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
