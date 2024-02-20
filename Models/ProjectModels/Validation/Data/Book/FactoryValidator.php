<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Book;

use Interfaces\Book\BookDataValidatorInterface;
use Models\ProjectModels\Validation\Data\Book\Admin;
//use mysql_xdevapi\Exception;

class FactoryValidator
{
    /**
     * @param string $userType
     * @param string $actionType
     * @return BookDataValidatorInterface
     * @throws \Exception
     */
    public static function getValidator(string $userType, string $actionType): BookDataValidatorInterface
    {
        if (strtolower($userType) === 'admin') {
            switch (strtolower($actionType)) {
                case 'add' :
                    return new Admin\Add\Validator();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($userType) === 'user') {
            switch (strtolower($actionType)) {
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $userType" . 'Controller' . " doesn't exist!"
                    );
            }
        } else {
            throw new \Exception("UserType : '$userType' doesn't exist!");
        }
    }
}
