<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\HeadAdmin;

use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel;

class SessionModel extends AbstractSessionModel
{
    private static SessionModel $selfInstance;

    protected function __construct()
    {
        parent::__construct();
    }

    protected static function createSelf(): AbstractSessionModel
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }

    protected function setUserType(): void
    {
        $this->userType = 'head_admin';
    }
}
