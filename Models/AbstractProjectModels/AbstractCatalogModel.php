<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractCatalogModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;
    private const CATALOG_FIELDS = [
        'books_catalog`.`image' => 'book_image',
        'books_catalog`.`title' => 'book_title',
        'authors`.`name' => 'book_author',
        'genres`.`title' => 'book_genre',
        'books_catalog`.`pub_date' => 'book_pub_date',
        'books_catalog`.`rating' => 'book_rating',
        'books_catalog`.`price' => 'book_price',
        'books_catalog`.`quantity' => 'book_quantity'
    ];
    protected const CATALOG_JOIN_TABLES = [
        'genres',
        'authors'
    ];
    protected const CATALOG_JOIN_CONDITIONS = [
        'books_catalog`.`genre_id' => 'genres`.`id',
        'books_catalog`.`author_id' => 'authors`.`id'
    ];
    protected array $catalogFields = [];

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
        $this->catalogFields = self::CATALOG_FIELDS;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function catalog(): ?array
    {
        $requestFields = $this->getCatalogFields();
        $joinTables = self::CATALOG_JOIN_TABLES;
        $joinConditions = self::CATALOG_JOIN_CONDITIONS;
        $joinTypes = [
            'JOIN',
            'JOIN'
        ];
        $dbResult =
            $this->db->select($requestFields)->
            from(['books_catalog'],$joinTables,$joinConditions,$joinTypes)->query()->fetchAll();
        if (!$dbResult) {
            $dbResult = null;
            $this->msgModel->setMsg('empty', 'catalog', 'empty_catalog');
        }


        return $dbResult;
    }

    protected function getCatalogFields(): ?array
    {
        return $this->catalogFields;
    }
}
