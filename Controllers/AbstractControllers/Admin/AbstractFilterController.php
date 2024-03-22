<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers\Admin;

use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel;
use Controllers\AbstractControllers\AbstractFilterController as BaseController;
use Models\AbstractProjectModels\Admin\AbstractFilterModel;

abstract class AbstractFilterController extends BaseController
{
    public function __construct(AbstractFilterModel $filterModel, AbstractSessionModel $sessionModel)
    {
        parent::__construct($filterModel, $sessionModel);
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
