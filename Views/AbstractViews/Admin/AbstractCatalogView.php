<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel as AdminSessionModel;
use Views\AbstractViews\AbstractCatalogView as BaseCatalogView;

abstract class AbstractCatalogView extends BaseCatalogView
{
    protected const ADMIN_LAYOUTS = 'admin/';

    public function __construct(AdminSessionModel $adminSessionModel)
    {
        parent::__construct($adminSessionModel);
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
        return parent::getContentPath() . $this->getAdminLayouts();
    }

    protected function getAdminLayouts(): string
    {
        return self::ADMIN_LAYOUTS;
    }
}
