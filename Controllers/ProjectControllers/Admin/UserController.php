<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Models\ProjectModels\Admin\UserModel;
use Views\ProjectViews\Admin\UserView;
use Controllers\AbstractControllers\AbstractUserController;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView());
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
        if ($this->userModel->getSessModel()->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHome();
        } else {
            $this->redirect();
        }
    }

    protected function validateRequester(): bool
    {
        return !$this->userModel->getSessModel()->isAdmin();
    }
}
