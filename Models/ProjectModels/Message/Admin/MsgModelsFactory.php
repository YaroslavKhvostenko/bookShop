<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin;

use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;
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
        $nameSpace = self::NAME_SPACE . 'Admin' . self::isDefault($customerType);

        switch ($customerType) {
            case 'head_admin' :
                $nameSpace .= 'Head\\';
                break;
            case 'admin' :
                $nameSpace .= 'Admin\\';
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
                case 'administrate':
                    $nameSpace .= 'Administrate';
                    break;
                case 'provide':
                    $nameSpace .= 'Provide';
                    break;
                case 'redirect':
                    $nameSpace .= 'Redirect';
                    break;
                case 'remove':
                    $nameSpace .= 'Remove';
                    break;
                case 'task':
                    $nameSpace .= 'Task';
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
