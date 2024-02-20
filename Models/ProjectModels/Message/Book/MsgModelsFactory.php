<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Book;

use Models\AbstractProjectModels\Message\Book\AbstractBaseMsgModel;
use Models\ProjectModels\Message\Book\Add;
//use mysql_xdevapi\Exception;

class MsgModelsFactory
{
    /**
     * @param string $userType
     * @param string|null $actionType
     * @return AbstractBaseMsgModel|null
     * @throws \Exception
     */
    public static function getMsgModel(string $userType, string $actionType = null): AbstractBaseMsgModel
    {
        if (strtolower($userType) === 'user') {
            switch (strtolower($actionType)) {
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($userType) === 'admin') {
            switch (strtolower($actionType)) {
                case 'add' :
                    return new Add\Admin\MsgModel();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                    );
            }
        }  else {
            throw new \Exception("UserType : '$userType' doesn't exist!");
        }
    }
}
