<?php
declare(strict_types=1);

namespace Models\ProjectModels\Admin;

use Models\AbstractProjectModels\AbstractCatalogModel;
use Models\ProjectModels\Session\User\Admin\SessionModel;

class CatalogModel extends AbstractCatalogModel
{
    private const CATALOG_FIELDS = [
        'books_catalog`.`description' => 'book_description'
    ];

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
        $this->catalogFields = array_merge($this->catalogFields, self::CATALOG_FIELDS);
    }
}
