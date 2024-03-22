<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\AbstractCatalogModel;
use Models\ProjectModels\Session\User\SessionModel;

class CatalogModel extends AbstractCatalogModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance(), new FilterModel());
    }
}
