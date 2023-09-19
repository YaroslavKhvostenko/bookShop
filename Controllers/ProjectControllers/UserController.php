<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\UserModel;
use Views\ProjectViews\UserView;
use Models\ProjectModels\Validation\Data\User\Validator;
use Models\ProjectModels\Message\User\ResultMessageModel;
use Controllers\AbstractControllers\AbstractUserController;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView(), new Validator(), new ResultMessageModel());
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
}
