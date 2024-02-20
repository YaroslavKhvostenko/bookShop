<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\User\Admin;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;

class SessionModel extends AbstractSessionModel
{
    private static SessionModel $selfInstance;
    private const DATA_FIELDS = [
        'is_admin' => 'is_admin',
        'is_approved' => 'is_approved',
        'is_head' => 'is_head'
    ];

    protected function __construct()
    {
        parent::__construct();
        $this->dataFields = array_merge($this->dataFields, self::DATA_FIELDS);
    }

    protected static function createSelf(): AbstractSessionModel
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }

    public function isHeadAdmin(): bool
    {
        if (isset($this->data['is_head'])) {
            return $this->data['is_head'] === '1';
        }

        return false;
    }

    public function isApproved(): bool
    {
        if (isset($this->data['is_approved'])) {
            return $this->data['is_approved'] === '1';
        }

        return false;
    }
}
