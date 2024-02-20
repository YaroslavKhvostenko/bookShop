<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin;

use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;
use Models\ProjectModels\Message\Admin\Head\Administrate;
use Models\ProjectModels\Message\Admin\Head\Provide;
use Models\ProjectModels\Message\Admin\Head\Redirect;
use Models\ProjectModels\Message\Admin\Head\Remove;
//use mysql_xdevapi\Exception;

class MsgModelsFactory
{
    /**
     * @param string $adminType
     * @param string|null $actionType
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    public static function getMsgModel(string $adminType, string $actionType = null): AbstractBaseMsgModel
    {
        if (strtolower($adminType) === 'head_admin') {
            switch (strtolower($actionType)) {
                case 'administrate' :
                    return new Administrate\MsgModel();
                case 'provide' :
                    return new Provide\MsgModel();
                case 'redirect' :
                    return new Redirect\MsgModel();
                case 'remove' :
                    return new Remove\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $adminType" . 'AdminController' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($adminType) === 'admin') {
            switch (strtolower($actionType)) {
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $adminType/ " . 'AdminController' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($adminType) === 'default') {
            return new DefaultMsgModel();
        } else {
            throw new \Exception("AdminType : '$adminType' doesn't exist!");
        }
    }
}
