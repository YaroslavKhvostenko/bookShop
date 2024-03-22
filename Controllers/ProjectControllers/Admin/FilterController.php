<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\Admin\AbstractFilterController;
use Models\ProjectModels\Admin\FilterModel;
use Models\ProjectModels\Session\Admin\SessionModel;

class FilterController extends AbstractFilterController
{
    public function __construct()
    {
        parent::__construct(new FilterModel, SessionModel::getInstance());
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('admin/' . $url);
    }

    protected function validateRequester(): bool
    {
        return (parent::validateRequester() || $this->sessionModel->isHeadAdmin());
    }
}