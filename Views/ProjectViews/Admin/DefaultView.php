<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\Admin\AbstractDefaultView;
use Models\ProjectModels\Session\Admin\SessionModel;

class DefaultView extends AbstractDefaultView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
