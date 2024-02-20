<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;
use Models\ProjectModels\Session\User\Admin\SessionModel as AdminSessModel;
use Views\AbstractViews\AbstractCatalogView as BaseCatalogView;

abstract class AbstractCatalogView extends BaseCatalogView
{
    protected const ADMIN_LAYOUTS = 'admin/';

    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), AdminSessModel::getInstance());
    }

    protected function getContentPath(): string
    {
        return parent::getContentPath() . self::ADMIN_LAYOUTS;
    }

    protected function getHeaderPath(): string
    {
        return parent::getHeaderPath() . self::ADMIN_LAYOUTS;
    }
}
