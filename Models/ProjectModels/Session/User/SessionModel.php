<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\User;

use Models\AbstractProjectModels\Session\AbstractSessionModel;

abstract class SessionModel extends AbstractSessionModel
{
    protected const SESS_FIELD = 'user';
    private static SessionModel $selfInstance;

}
