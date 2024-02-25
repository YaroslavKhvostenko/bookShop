<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\AbstractIndexController;
use Views\ProjectViews\Admin\DefaultView;
use Models\ProjectModels\Session\Admin\SessionModel;

class IndexController extends AbstractIndexController
{
    public function __construct()
    {
        parent::__construct(new DefaultView(), SessionModel::getInstance());
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect();
    }

    protected function validateRequest(): bool
    {
        return $this->sessionModel->isLoggedIn() && !$this->sessionModel->isAdmin();
    }
}
