<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\Admin\AbstractCatalogController;
use Models\ProjectModels\Admin\CatalogModel;
use Views\ProjectViews\Admin\CatalogView;
use Models\ProjectModels\Session\Admin\SessionModel;

class CatalogController extends AbstractCatalogController
{
    public function __construct()
    {
        parent::__construct(new CatalogModel(), new CatalogView(), SessionModel::getInstance());
    }

    protected function validateRequester(): bool
    {
        return (
            parent::validateRequester() ||
            !$this->sessionModel->isApproved() ||
            $this->sessionModel->isHeadAdmin()
        );
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('admin/' . $url);
    }
}
