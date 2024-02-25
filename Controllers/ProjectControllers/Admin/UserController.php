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
        $this->redirect('admin/' . $url);
    }

    protected function logoutByCustomerType(): void
    {
        if ($this->sessionModel->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHome();
        } else {
            $this->redirect();
        }
    }

    protected function validateRequester(): bool
    {
        return !$this->sessionModel->isAdmin();
    }
}
