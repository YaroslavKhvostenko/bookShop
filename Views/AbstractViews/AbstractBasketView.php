<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\ProjectModels\Session\User\SessionModel;

abstract class AbstractBasketView extends AbstractDefaultView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
