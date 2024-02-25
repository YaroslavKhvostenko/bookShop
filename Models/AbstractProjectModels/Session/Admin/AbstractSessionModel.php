<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Session\Admin;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel as BaseSessionModel;

abstract class AbstractSessionModel extends BaseSessionModel
{
    protected string $userType = 'guest_admin';

    protected function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(): AbstractSessionModel
    {
        return static::createSelf();
    }

    public function isHeadAdmin(): bool
    {
        if (isset($this->data['is_head'])) {
            return $this->data['is_head'] === '1';
        }

        return false;
    }
}
