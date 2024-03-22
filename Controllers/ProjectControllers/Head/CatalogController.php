<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Head;

use Controllers\AbstractControllers\Admin\AbstractCatalogController;
use Models\ProjectModels\HeadAdmin\CatalogModel;
use Views\ProjectViews\HeadAdmin\CatalogView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class CatalogController extends AbstractCatalogController
{
    public function __construct()
    {
        parent::__construct(new CatalogModel(), new CatalogView(), SessionModel::getInstance());
    }

    protected function validateRequester(): bool
    {
        return (parent::validateRequester() || !$this->sessionModel->isHeadAdmin());
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('head/' . $url);
    }
}
