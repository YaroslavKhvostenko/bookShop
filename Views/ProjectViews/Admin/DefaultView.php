<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\AbstractDefaultView;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;
use Models\ProjectModels\Session\User\Admin\SessionModel as AdminSessModel;

class DefaultView extends AbstractDefaultView
{
    protected const ADMIN_LAYOUTS = 'admin/';

    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), AdminSessModel::getInstance());
    }

    protected function getHeaderPath(): string
    {
        $headerPath = parent::getHeaderPath() . $this->getAdminPath();
        if ($this->userSessModel->isHeadAdmin()) {
            $headerPath .= 'head/';
        }

        return $headerPath;
    }

    protected function getContentPath(): string
    {
        return $this->getPath() . $this->getAdminPath();
    }

    protected function getAdminPath(): string
    {
        return self::ADMIN_LAYOUTS;
    }
}
