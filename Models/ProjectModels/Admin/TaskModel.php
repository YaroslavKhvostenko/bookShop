<?php
declare(strict_types=1);

namespace Models\ProjectModels\Admin;

use Models\AbstractProjectModels\Admin\AbstractTaskModel;
use Models\ProjectModels\Session\Admin\SessionModel;

class TaskModel extends AbstractTaskModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
