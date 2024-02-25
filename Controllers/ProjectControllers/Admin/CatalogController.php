<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\AbstractCatalogController;
use Models\ProjectModels\Admin\CatalogModel;
use Views\ProjectViews\Admin\CatalogView;
use Models\ProjectModels\Session\Admin\SessionModel;

class CatalogController extends AbstractCatalogController
{
    public function __construct()
    {
        parent::__construct(new CatalogModel(), new CatalogView(), SessionModel::getInstance());
    }

    protected function validateRequest(): bool
    {
        return (
            !$this->sessionModel->isLoggedIn() ||
            !$this->sessionModel->isAdmin() ||
            $this->sessionModel->isHeadAdmin()
        );
    }

    protected function prepareRedirect(string $url = null): void
    {
        if ($this->sessionModel->isAdmin()) {
            $this->redirect('admin/');
        } else {
            $this->redirect();
        }
    }
}
