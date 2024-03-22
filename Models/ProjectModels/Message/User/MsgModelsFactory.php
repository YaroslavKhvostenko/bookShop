<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
//use Models\ProjectModels\Message\User\Admin;
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
        $nameSpace = self::NAME_SPACE . 'User' . self::isDefault($customerType);

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
            case 'default' :
                $className = 'DefaultMsgModel';
                break;
            default :
                break;
        }

        if (!is_null($actionType)) {
            $actionType = strtolower($actionType);
            switch ($actionType) {
                case 'add' :
                    $nameSpace .= 'Add';
                    break;
                case 'authorization' :
                    $nameSpace .= 'Authorization';
                    break;
                case 'change' :
                    $nameSpace .= 'Change';
                    break;
                case 'registration' :
                    $nameSpace .= 'Registration';
                    break;
                case 'remove' :
                    $nameSpace .= 'Remove';
                    break;
                default :
                    break;
            }
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);

        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
