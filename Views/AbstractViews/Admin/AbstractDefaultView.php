<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel as AdminSessionModel;
use Views\AbstractViews\AbstractDefaultView as BaseDefaultView;

abstract class AbstractDefaultView extends BaseDefaultView
{
    public function __construct(AdminSessionModel $adminSessionModel)
    {
        parent::__construct($adminSessionModel);
    }

    protected function getContentPath(): string
    {
        return $this->getPath() . 'admin/';
    }
}
