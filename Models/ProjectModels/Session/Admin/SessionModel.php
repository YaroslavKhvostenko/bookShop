<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\Admin;

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

    public function isApproved(): bool
    {
        if (isset($this->data['is_approved'])) {
            return $this->data['is_approved'] === '1';
        }

        return false;
    }
}
