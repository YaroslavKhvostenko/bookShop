<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Views\AbstractViews\AbstractUserView as BaseUserView;
use Models\ProjectModels\Session\Admin\SessionModel;

abstract class AbstractUserView extends BaseUserView
{
    private const AVATAR_ADDRESS = 'admin_users/';
    protected const ADMIN_LAYOUTS = 'admin/';

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
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

    protected function getAvatarAddress(string $avatarTitle): string
    {
        return self::IMAGES_ADDRESS . self::AVATAR_ADDRESS . $avatarTitle ;
    }
}
