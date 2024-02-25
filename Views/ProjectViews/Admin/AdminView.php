<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Models\ProjectModels\Session\Admin\SessionModel;
use Views\AbstractViews\Admin\AbstractAdminView;

class AdminView extends AbstractAdminView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
