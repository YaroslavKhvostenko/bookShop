<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\AbstractAdminView;

class AdminView extends AbstractAdminView
{
    protected function getAdminPath(): string
    {
        return self::ADMIN_LAYOUTS;
    }
}
