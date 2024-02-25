<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Models\ProjectModels\Session\Admin\SessionModel;
use Views\AbstractViews\Admin\AbstractCatalogView;

class CatalogView extends AbstractCatalogView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    public function getPubDate(string $pubDate): ?string
    {
        return parent::getPubDate($pubDate);
    }
}
