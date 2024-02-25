<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel as AdminSessionModel;

abstract class AbstractAdminView extends AbstractDefaultView
{
    private const ADMIN_LAYOUTS = '';

    public function __construct(AdminSessionModel $userSessModel)
    {
        parent::__construct($userSessModel);
    }

    protected function getHeaderPath(): string
    {
        if ($this->userSessModel->isHeadAdmin()) {
            return 'head_admin/';
        }

        return 'admin/';
    }

    protected function getContentPath(): string
    {
        return parent::getContentPath() . self::getAdminLayouts();
    }

    protected static function getAdminLayouts(): string
    {
        return static::ADMIN_LAYOUTS;
    }
}
