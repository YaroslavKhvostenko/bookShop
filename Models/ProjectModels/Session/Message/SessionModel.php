<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\Message;

use Models\AbstractProjectModels\Session\Message\AbstractSessionModel;

class SessionModel extends AbstractSessionModel
{
    private static SessionModel $selfInstance;

    protected static function createSelf(): SessionModel
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }
}
