<?php
declare(strict_types=1);

namespace Views\ProjectViews\HeadAdmin;

use Views\AbstractViews\Admin\AbstractCatalogView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class CatalogView extends AbstractCatalogView
{
    protected const ADMIN_LAYOUTS = 'head_admin/';

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    public function getPubDate(string $pubDate): ?string
    {
        return parent::getPubDate($pubDate);
    }
}
