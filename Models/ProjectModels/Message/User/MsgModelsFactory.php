<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Message\User;
use Models\ProjectModels\Message\User\Admin;
//use mysql_xdevapi\Exception;

class MsgModelsFactory
{
    /**
     * @param string $userType
     * @param string|null $actionType
     * @return AbstractBaseMsgModel|null
     * @throws \Exception
     */
    public static function getMsgModel(string $userType, string $actionType = null): ?AbstractBaseMsgModel
    {
        if (strtolower($userType) === 'user') {
            switch (strtolower($actionType)) {
                case 'registration' :
                    return new User\Registration\MsgModel();
                case 'authorization' :
                    return new User\Authorization\MsgModel();
                case 'add' :
                    return new User\Add\MsgModel();
                case 'change' :
                    return new User\Change\MsgModel();
                case 'remove' :
                    return new User\Remove\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($userType) === 'admin') {
            switch (strtolower($actionType)) {
                case 'registration' :
                    return new Admin\Registration\MsgModel();
                case 'authorization' :
                    return new Admin\Authorization\MsgModel();
                case 'add' :
                    return new Admin\Add\MsgModel();
                case 'change' :
                    return new Admin\Change\MsgModel();
                case 'remove' :
                    return new Admin\Remove\MsgModel();
                default :
                    throw new \Exception(
                    "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                );
            }
        } elseif (strtolower($userType) === 'default') {
            return new DefaultMsgModel();
        } else {
            throw new \Exception("UserType : '$userType' doesn't exist!");
        }
    }
}
