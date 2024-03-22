<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Base;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\AbstractProjectModels\Message\AbstractMsgModelsFactory;
use Models\ProjectModels\Message\User\Admin;
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
        $className = '';
        $nameSpace = self::NAME_SPACE . 'Base' . self::isDefault($customerType);

        switch ($customerType) {
            case 'default' :
                $className = 'DefaultMsgModel';
                break;
            default :
                break;
        }

        $classNameWithNamespace = self::createClassDirectoryPath($nameSpace, $className);

        self::isClassExist($classNameWithNamespace);

        return new $classNameWithNamespace();
    }
}
