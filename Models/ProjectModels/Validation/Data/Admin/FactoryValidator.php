<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Admin;

use Interfaces\Admin\AdminDataValidatorInterface;
use Models\ProjectModels\Validation\Data\Admin\Head\Provide;
use Models\ProjectModels\Validation\Data\Admin\Head\Remove;
use Models\ProjectModels\Validation\Data\Admin\Head\Redirect;
//use mysql_xdevapi\Exception;

class FactoryValidator
{
    /**
     * @param string $adminType
     * @param string $actionType
     * @return AdminDataValidatorInterface
     * @throws \Exception
     */
    public static function getValidator(string $adminType, string $actionType): AdminDataValidatorInterface
    {
        if (strtolower($adminType) === 'head_admin') {
            switch (strtolower($actionType)) {
                case 'provide' :
                    return new Provide\Validator();
                case 'remove' :
                    return new Remove\Validator();
                case 'redirect' :
                    return new Redirect\Validator();
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $adminType" . 'Controller' . " doesn't exist!"
                    );
            }
        } elseif (strtolower($adminType) === 'admin') {
            switch (strtolower($actionType)) {
                default :
                    throw new \Exception(
                        "ActionType : '$actionType' in $adminType" . 'Controller' . " doesn't exist!"
                    );
            }
        } else {
            throw new \Exception("UserType : '$adminType' doesn't exist!");
        }
    }
}
