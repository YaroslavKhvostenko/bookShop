<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractFilterController;
use Models\ProjectModels\Session\User\SessionModel;
use Models\ProjectModels\FilterModel;

class FilterController extends AbstractFilterController
{
    public function __construct()
    {
        parent::__construct(new FilterModel(), SessionModel::getInstance());
    }

    protected function validateRequester(): bool
    {
        return $this->sessionModel->isAdmin();
    }
    protected function redirectHome(): void
    {
        $this->redirect();
    }
}
