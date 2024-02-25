<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel as AdminSessionModel;
use Views\AbstractViews\AbstractDefaultView as BaseDefaultView;

abstract class AbstractDefaultView extends BaseDefaultView
{
    protected const ADMIN_LAYOUTS = 'admin/';

    public function __construct(AdminSessionModel $adminSessionModel)
    {
        parent::__construct($adminSessionModel);
    }

    protected function getContentPath(): string
    {
        return parent::getContentPath() . $this->getAdminLayouts();
    }

    protected function getHeaderPath(): string
    {
        if ($this->userSessModel->isHeadAdmin()) {
            return 'head_admin/';
        }

        return 'admin/';
    }

    protected function getAdminLayouts(): string
    {
        return self::ADMIN_LAYOUTS;
    }
}
