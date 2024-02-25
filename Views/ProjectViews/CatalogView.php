<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractCatalogView;
use Models\ProjectModels\Session\User\SessionModel;

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
