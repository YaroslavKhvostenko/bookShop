<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\Admin\AbstractAdminController;
use Models\ProjectModels\Session\Admin\SessionModel;
use Models\ProjectModels\Admin\AdminModel;
use Views\ProjectViews\Admin\AdminView;

class AdminController extends AbstractAdminController
{
    public function __construct()
    {
        parent::__construct(new AdminModel(), new AdminView(), SessionModel::getInstance());
    }

    protected function validateRequester(): bool
    {
        return !$this->sessionModel->isAdmin() ||
            !$this->sessionModel->isApproved() ||
            $this->sessionModel->isHeadAdmin();
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('admin/'. $url);
    }
}
