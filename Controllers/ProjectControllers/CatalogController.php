<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractCatalogController;
use Models\ProjectModels\CatalogModel;
use Views\ProjectViews\CatalogView;
use Models\ProjectModels\Session\User\SessionModel;

class CatalogController extends AbstractCatalogController
{

    public function __construct()
    {
        parent::__construct(new CatalogModel(), new CatalogView(), SessionModel::getInstance());
    }

    protected function validateRequest(): bool
    {
        return $this->sessionModel->isAdmin();
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
