<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\Admin\AbstractTaskController;
use Models\ProjectModels\Session\Admin\SessionModel;
use Models\ProjectModels\Admin\TaskModel;
use Views\ProjectViews\Admin\TaskView;

class TaskController extends AbstractTaskController
{
    public function __construct()
    {
        parent::__construct(new TaskModel(), new TaskView(), SessionModel::getInstance());
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