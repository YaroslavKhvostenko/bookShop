<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers\Admin;

use Controllers\AbstractControllers\AbstractCatalogController as BaseController;
use Models\AbstractProjectModels\AbstractCatalogModel;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel;
use Views\AbstractViews\Admin\AbstractCatalogView;

abstract class AbstractCatalogController extends BaseController
{
    public function __construct(
        AbstractCatalogModel $catalogModel,
        AbstractCatalogView $catalogView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($catalogModel, $catalogView, $sessionModel);
    }

    protected function validateRequester(): bool
    {
        return (!$this->sessionModel->isLoggedIn() || !$this->sessionModel->isAdmin());
    }

    protected function redirectHome(): void
    {
        $this->redirect('admin/');
    }
}
