<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\Admin\AbstractTaskView;
use Models\ProjectModels\Session\Admin\SessionModel;

class TaskView extends AbstractTaskView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
