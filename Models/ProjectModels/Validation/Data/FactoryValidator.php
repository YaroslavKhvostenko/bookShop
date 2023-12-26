<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data;

use Interfaces\User\UserDataValidatorInterface;
use Models\ProjectModels\Validation\Data\User;
use Models\ProjectModels\Validation\Data\User\Admin;
//use mysql_xdevapi\Exception;

class FactoryValidator
{
    /**
     * @param string $userType
     * @param string $actionType
     * @return UserDataValidatorInterface
     * @throws \Exception
     */
    public static function getValidator(string $userType, string $actionType): UserDataValidatorInterface
    {
        if (strtolower($userType) === 'user') {
            switch (strtolower($actionType)) {
                case 'authorization' : return new User\Authorization\Validator();
                case 'registration' : return new User\Registration\Validator();
                case 'change' : return new User\Change\Validator();
                case 'add' : return new User\Add\Validator();
                default :
                    throw new \Exception("ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!");
            }
        } elseif (strtolower($userType) === 'admin') {
            switch (strtolower($actionType)) {
                case 'authorization' : return new Admin\Authorization\Validator();
                case 'registration' : return new Admin\Registration\Validator();
                case 'change' : return new Admin\Change\Validator();
                case 'add' : return new Admin\Add\Validator();
                default :
                    throw new \Exception("ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!");
            }
        } else {
            throw new \Exception("UserType : '$userType' doesn't exist!");
        }
    }
}
