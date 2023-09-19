<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\AbstractDefaultView;

class DefaultView extends AbstractDefaultView
{
    protected const ADMIN_LAYOUTS = 'admin/';

    protected function getContentPath(): string
    {
        return $this->getPath() . $this->getAdminPath();
    }

    protected function getAdminPath(): string
    {
        return self::ADMIN_LAYOUTS;
    }
}
