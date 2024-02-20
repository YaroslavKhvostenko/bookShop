<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel; // ПЕРЕДЕЛАТЬ ПОД АДМИНОВСКУЮ ЧАСТЬ ДЕЯТЕЛЬНОСТИ
use Models\ProjectModels\Session\User\Admin\SessionModel as AdminSessModel; // ПЕРЕДЕЛАТЬ ПОД АДМИНОВСКУЮ ЧАСТЬ дЕЯТЕЛЬНОСТИ

abstract class AbstractAdminView extends AbstractDefaultView
{
    protected const ADMIN_LAYOUTS = 'admin/';

    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), AdminSessModel::getInstance());
    }

    protected function getContentPath(): string
    {
        return $this->getPath() . $this->getAdminPath();
    }

//    public function getHeaderContent(): void
//    {
//        if ($this->userSessModel->isHeadAdmin()) {
//            include_once $this->getContentPath() . 'user_logged_header.phtml';
//        } else {
//            include_once $this->getContentPath() . 'user_header.phtml';
//        }
//    }

    protected function getAdminPath(): string
    {
        return self::ADMIN_LAYOUTS;
    }
//    abstract protected function getAdminPath(): string;
}
