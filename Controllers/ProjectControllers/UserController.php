<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\UserModel;
use Views\ProjectViews\UserView;
use Controllers\AbstractControllers\AbstractUserController;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView());
    }

    protected function redirectHome(): void
    {
        $this->redirect();
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }


    protected function logoutByCustomerType(): void
    {
        if (!$this->userModel->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHome();
        } else {
            $this->redirect('admin/');
        }
    }

    protected function validateRequester(): bool
    {
        return $this->userModel->isAdmin();
    }
}
