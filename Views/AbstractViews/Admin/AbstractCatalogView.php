<?php
declare(strict_types=1);

namespace Views\AbstractViews\Admin;

use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel as AdminSessionModel;
use Views\AbstractViews\AbstractCatalogView as BaseCatalogView;
use Models\AbstractProjectModels\Admin\AbstractFilterModel;

abstract class AbstractCatalogView extends BaseCatalogView
{
    public function __construct(AdminSessionModel $adminSessionModel, AbstractFilterModel $filterModel)
    {
        parent::__construct($adminSessionModel, $filterModel);
    }
}
