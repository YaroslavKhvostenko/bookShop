<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Models\ProjectModels\Admin\UserModel;
use Views\ProjectViews\Admin\UserView;
use Controllers\AbstractControllers\AbstractUserController;
use Models\ProjectModels\Session\Admin\SessionModel;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView(), SessionModel::getInstance());
    }

    protected function redirectHome(): void
    {
        $this->redirect('admin/');
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('admin/' . $url);
    }

    protected function validateRequester(): bool
    {
        return !$this->sessionModel->isAdmin();
    }

    protected function logoutRedirect(): void
    {
        $this->redirect();
    }
}
