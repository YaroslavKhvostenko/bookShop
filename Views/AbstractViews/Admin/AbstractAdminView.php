<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel as AdminSessionModel;

abstract class AbstractAdminView extends AbstractDefaultView
{
    public function __construct(AdminSessionModel $userSessModel)
    {
        parent::__construct($userSessModel);
    }
}
