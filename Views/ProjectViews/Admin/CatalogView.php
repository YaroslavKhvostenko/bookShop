<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Models\ProjectModels\Session\Admin\SessionModel;
use Views\AbstractViews\Admin\AbstractCatalogView;
use Models\ProjectModels\Admin\FilterModel;

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
