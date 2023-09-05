<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\UserModel;
use Views\ProjectViews\UserView;
use Models\ProjectModels\Validation\Data\User\Validator;
use Models\ProjectModels\Message\User\ResultMessageModel;
use Controllers\AbstractControllers\AbstractUserController;

/**
 * @package Controllers\ProjectControllers
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
            $this->location('admin/');
        } else {
            $this->redirectHomeLocation();
        }
    }

    protected function redirectHomeLocation(): void
    {
        $this->location();
    }

    protected function redirectLocation(string $url = null): void
    {
        $this->location($url);
    }


    protected function logoutActionType(): void
    {
        if (!$this->userModel->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHomeLocation();
        } else {
            $this->location('admin/');
        }
    }
}
