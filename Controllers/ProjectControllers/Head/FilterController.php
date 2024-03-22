<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Head;

use Controllers\AbstractControllers\Admin\AbstractFilterController;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;
use Models\ProjectModels\HeadAdmin\FilterModel;

class FilterController extends AbstractFilterController
{
    public function __construct()
    {
        parent::__construct(new FilterModel, SessionModel::getInstance());
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('head/' . $url);
    }

    protected function validateRequester(): bool
    {
        return (parent::validateRequester() || !$this->sessionModel->isHeadAdmin());
    }
}