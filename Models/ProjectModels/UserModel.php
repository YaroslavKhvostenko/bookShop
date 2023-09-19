<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\AbstractUserModel;
use Models\ProjectModels\Message\User\ResultMessageModel;

class UserModel extends AbstractUserModel
{
    public function __construct()
    {
        parent::__construct(new ResultMessageModel());
    }
}
