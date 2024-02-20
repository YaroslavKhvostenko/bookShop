<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\AbstractUserView;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;
use Models\ProjectModels\Session\User\Admin\SessionModel as AdminSessModel;

class UserView extends AbstractUserView
{
    protected const ADMIN_LAYOUTS = 'admin/';
    private const AVATAR_ADDRESS = 'admin_users/';
    private const REQUIRED_PROFILE_ITEMS = [
        'phone' => 'phone',
        'address' => 'address'
    ];

    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), AdminSessModel::getInstance());
        $this->profileItems['required'] = array_merge(
            $this->profileItems['required'], self::REQUIRED_PROFILE_ITEMS
        );
    }

    protected function getAvatarAddress(string $avatarTitle = null): string
    {
        return parent::getAvatarAddress() . self::AVATAR_ADDRESS . $avatarTitle;
    }

    protected function getHeaderPath(): string
    {
        $headerPath = parent::getHeaderPath() . $this->getAdminPath();
        if ($this->userSessModel->isHeadAdmin()) {
            $headerPath .= 'head/';
        }

        return $headerPath;
    }

//    protected function getRequestController(): string
//    {
//        return '/' . $this->getRequestUserType() . parent::getRequestController();
//    }

    protected function getContentPath(): string
    {
        return $this->getPath() . $this->getAdminPath();
    }

    protected function getAdminPath(): string
    {
        return self::ADMIN_LAYOUTS;
    }
}
