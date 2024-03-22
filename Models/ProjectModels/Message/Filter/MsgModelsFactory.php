<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Filter;

use Models\AbstractProjectModels\Message\Filter\AbstractBaseMsgModel;
use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;

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
        $nameSpace = self::NAME_SPACE . 'Filter' . self::isDefault($customerType);
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
                case 'set' :
                    $nameSpace .= 'Set';
                    break;
                case 'clear' :
                    $nameSpace .= 'Clear';
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
