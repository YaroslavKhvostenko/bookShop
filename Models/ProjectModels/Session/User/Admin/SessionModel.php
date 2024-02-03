<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\User\Admin;

use Models\ProjectModels\Session\User\SessionModel as BaseSessionModel;

abstract class SessionModel extends BaseSessionModel
{
    private static SessionModel $selfInstance;
}
