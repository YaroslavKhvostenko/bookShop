<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Head;

use Controllers\AbstractControllers\AbstractCatalogController;
use Models\ProjectModels\HeadAdmin\CatalogModel;
use Views\ProjectViews\HeadAdmin\CatalogView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

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
            !$this->sessionModel->isHeadAdmin()
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
