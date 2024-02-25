<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractDefaultView;
use Models\ProjectModels\Session\User\SessionModel;

class DefaultView extends AbstractDefaultView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
