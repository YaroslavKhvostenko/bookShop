<?php
declare(strict_types=1);

namespace Views\ProjectViews\HeadAdmin;

use Views\AbstractViews\Admin\AbstractTaskView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class TaskView extends AbstractTaskView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
