<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Catalog;

use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;
use Models\ProjectModels\Message\Catalog\Show;
//use mysql_xdevapi\Exception;

class MsgModelsFactory
{

    public static function getMsgModel(string $customerType, string $actionType = null): AbstractBaseMsgModel
    {
        if (strtolower($customerType) === 'head_admin') {
            switch (strtolower($actionType)) {
                case 'show' :
                    return new Show\Admin\Head\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $customerType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($customerType) === 'admin') {
            switch (strtolower($actionType)) {
                case 'show' :
                    return new Show\Admin\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $customerType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($customerType) === 'user') {
            switch (strtolower($actionType)) {
                case 'show' :
                    return new Show\User\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $customerType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($customerType) === 'not_logged') {
            switch (strtolower($actionType)) {
                case 'show' :
                    return new Show\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $customerType" . 'Controller' . " doesn't exist!"
                    );
            }
        } else {
            throw new \Exception("UserType : '$customerType' doesn't exist!");
        }
    }
}
