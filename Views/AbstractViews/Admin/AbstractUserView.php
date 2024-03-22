<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Views\AbstractViews\AbstractUserView as BaseUserView;
use Models\ProjectModels\Session\Admin\SessionModel;

abstract class AbstractUserView extends BaseUserView
{
    private const AVATAR_ADDRESS = 'admin_users/';

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    protected function getContentPath(): string
    {
        return $this->getPath() . 'admin/';
    }

    protected function getAvatarAddress(string $avatarTitle): string
    {
        return self::IMAGES_ADDRESS . self::AVATAR_ADDRESS . $avatarTitle ;
    }
}
