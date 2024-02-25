<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractIndexController;
use Views\ProjectViews\DefaultView;
use Models\ProjectModels\Session\User\SessionModel;

class IndexController extends AbstractIndexController
{
    public function __construct()
    {
        parent::__construct(new DefaultView(), SessionModel::getInstance());
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect('admin/');
    }

    protected function validateRequest(): bool
    {
        return $this->sessionModel->isLoggedIn() &&
            $this->sessionModel->isAdmin();
    }
}
