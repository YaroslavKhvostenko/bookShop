<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Models\ProjectModels\Admin\UserModel;
use Views\ProjectViews\Admin\UserView;
use Models\ProjectModels\Validation\Data\User\Admin\Validator;
use Models\ProjectModels\Message\User\Admin\ResultMessageModel;
use Controllers\AbstractControllers\AbstractUserController;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView(), new Validator(), new ResultMessageModel());
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
        if ($this->userModel->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHome();
        } else {
            $this->redirect();
        }
    }
}
