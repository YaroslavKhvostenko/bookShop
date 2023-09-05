<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Models\ProjectModels\Admin\UserModel;
use Views\ProjectViews\Admin\UserView;
use Models\ProjectModels\Validation\Data\User\Admin\Validator;
use Models\ProjectModels\Message\User\Admin\ResultMessageModel;
use Controllers\AbstractControllers\AbstractUserController;

/**
 * @package Controllers\ProjectControllers\Admin
 */
class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView(), new Validator(), new ResultMessageModel());
    }

    /**
     * if user is logged sends to homeLocation depending on if it is a simple user or admin user
     */
    protected function homeLocationByCustomerType(): void
    {
        if ($this->userModel->isAdmin()) {
            $this->redirectHomeLocation();
        } else {
            $this->location();
        }
    }

    protected function redirectHomeLocation(): void
    {
        $this->location('admin/');
    }

    protected function redirectLocation(string $url = null): void
    {
        $this->location('admin/' . $url);
    }

    protected function logoutActionType(): void
    {
        if ($this->userModel->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHomeLocation();
        } else {
            $this->location();
        }
    }
}
