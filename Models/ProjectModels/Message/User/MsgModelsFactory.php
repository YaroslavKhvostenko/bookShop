<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Message\User\Add\MsgModel as UserAddProfileItemMsgModel;
use Models\ProjectModels\Message\User\Admin\Add\MsgModel as AdminAddProfileItemMsgModel;
use Models\ProjectModels\Message\User\Admin\Authorization\MsgModel as AdminAuthorizationMsgModel;
use Models\ProjectModels\Message\User\Admin\Change\MsgModel as AdminChangeProfileItemMsgModel;
use Models\ProjectModels\Message\User\Admin\Registration\MsgModel as AdminRegisterMsgModel;
use Models\ProjectModels\Message\User\Admin\Remove\MsgModel as AdminRemoveProfileItemMsgModel;
use Models\ProjectModels\Message\User\Authorization\MsgModel as UserAuthorizationMsgModel;
use Models\ProjectModels\Message\User\Change\MsgModel as UserChangeProfileItemMsgModel;
use Models\ProjectModels\Message\User\Registration\MsgModel as UserRegisterMsgModel;
use Models\ProjectModels\Message\User\Remove\MsgModel as UserRemoveProfileItemMsgModel;
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
                    return new UserRegisterMsgModel();
                case 'authorization' :
                    return new UserAuthorizationMsgModel();
                case 'add' :
                    return new UserAddProfileItemMsgModel();
                case 'change' :
                    return new UserChangeProfileItemMsgModel();
                case 'remove' :
                    return new UserRemoveProfileItemMsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($userType) === 'admin') {
            switch (strtolower($actionType)) {
                case 'registration' :
                    return new AdminRegisterMsgModel();
                case 'authorization' :
                    return new AdminAuthorizationMsgModel();
                case 'add' :
                    return new AdminAddProfileItemMsgModel();
                case 'change' :
                    return new AdminChangeProfileItemMsgModel();
                case 'remove' :
                    return new AdminRemoveProfileItemMsgModel();
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
