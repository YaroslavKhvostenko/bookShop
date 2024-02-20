<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractCatalogView;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;
use Models\ProjectModels\Session\User\SessionModel as UserSessModel;

class CatalogView extends AbstractCatalogView
{
    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), UserSessModel::getInstance());
    }

    public function getPubDate(string $pubDate): ?string
    {
        return parent::getPubDate($pubDate);
    }
}
