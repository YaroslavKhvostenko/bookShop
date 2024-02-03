<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\Message;

use Models\AbstractProjectModels\Session\AbstractSessionModel;

abstract class SessionModel extends AbstractSessionModel
{
    private const SESS_FIELD = 'resultMsg';
    private static SessionModel $selfInstance;
}
