<?php
declare(strict_types=1);

namespace Views\ProjectViews\HeadAdmin;

use Views\AbstractViews\Admin\AbstractCatalogView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;
use Models\ProjectModels\HeadAdmin\FilterModel;

class CatalogView extends AbstractCatalogView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance(), new FilterModel());
    }

    public function getPubDate(string $pubDate): ?string
    {
        return parent::getPubDate($pubDate);
    }
}
