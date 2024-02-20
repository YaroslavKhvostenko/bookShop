<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin\Head;

use Views\AbstractViews\Admin\AbstractCatalogView;
use Models\ProjectModels\Session\User\Admin\SessionModel as AdminSessModel;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;

class CatalogView extends AbstractCatalogView
{
    protected const HEAD_ADMIN_LAYOUTS = 'head/';

    protected function getContentPath(): string
    {
        return parent::getContentPath() . self::HEAD_ADMIN_LAYOUTS;
    }

    protected function getHeaderPath(): string
    {
        return parent::getHeaderPath() . self::HEAD_ADMIN_LAYOUTS;
    }

    public function getPubDate(string $pubDate): ?string
    {
        return parent::getPubDate($pubDate);
    }
}
