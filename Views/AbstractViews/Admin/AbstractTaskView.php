<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel;

abstract class AbstractTaskView extends AbstractDefaultView
{
    public function __construct(AbstractSessionModel $adminSessionModel)
    {
        parent::__construct($adminSessionModel);
    }
}
