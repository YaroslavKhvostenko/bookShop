<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel as UserSessionModel;

abstract class AbstractOrderView extends AbstractDefaultView
{
    public function __construct(UserSessionModel $userSessModel)
    {
        parent::__construct($userSessModel);
    }
}
