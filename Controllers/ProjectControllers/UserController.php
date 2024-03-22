<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\UserModel;
use Views\ProjectViews\UserView;
use Controllers\AbstractControllers\AbstractUserController;
use Models\ProjectModels\Session\User\SessionModel;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView(), SessionModel::getInstance());
    }

    protected function redirectHome(): void
    {
        $this->redirect();
    }

    protected function validateRequester(): bool
    {
        return $this->sessionModel->isAdmin();
    }

    protected function logoutRedirect(): void
    {
        $this->redirect('admin/');
    }
}
