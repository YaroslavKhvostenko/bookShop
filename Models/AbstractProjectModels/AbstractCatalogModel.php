<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractCatalogModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;
    protected AbstractFilterModel $filterModel;
    private const CATALOG_FIELDS = [
        'books_catalog`.`image' => 'book_image',
        'books_catalog`.`title' => 'book_title',
        'authors`.`name' => 'book_author',
        'genres`.`title' => 'book_genre',
        'books_catalog`.`pub_date' => 'book_pub_date',
        'books_catalog`.`rating' => 'book_rating',
        'books_catalog`.`price' => 'book_price',
        'books_catalog`.`quantity' => 'book_quantity',
        'books_catalog`.`id' => 'book_id'
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

    public function __construct(AbstractSessionModel $sessionModel, AbstractFilterModel $filterModel)
    {
        parent::__construct($sessionModel);
        $this->filterModel = $filterModel;
        $this->db = MySqlDbWorkModel::getInstance();
        $this->catalogFields = self::CATALOG_FIELDS;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getCatalog(): ?array
    {
        $requestFields = $this->getCatalogFields();
        $joinTables = self::CATALOG_JOIN_TABLES;
        $joinConditions = self::CATALOG_JOIN_CONDITIONS;
        $joinTypes = [
            'JOIN',
            'JOIN'
        ];
        $booksResult = $this->db->select($requestFields)->from(
                ['books_catalog'],
                $joinTables,
                $joinConditions,
                $joinTypes
            )->query()->fetchAll();

        if (!$booksResult) {
            $result['books'] = null;
            $result['books_exist'] = false;
            $this->msgModel->setMessage('empty', 'catalog', 'empty_catalog');

            return $result;
        }

        $result['books_exist'] = true;
        $result['authors'] = $this->db->select(['id', 'name'])->from(['authors'])->query()->fetchAll();
        $result['genres'] = $this->db->select(['id', 'title'])->from(['genres'])->query()->fetchAll();
        $controller = $this->serverInfo->getRequestController();
        $action = $this->serverInfo->getRequestAction();
        if ($this->filterModel->issetFilter($controller, $action)) {
            $filterData = $this->filterModel->getFilter($controller, $action);
            $booksResult = $this->db->select($requestFields)->from(
                ['books_catalog'],
                $joinTables,
                $joinConditions,
                $joinTypes
            );
            if (!is_null($filterData['condition_data'])) {
                $booksResult = $booksResult->condition(
                    $filterData['condition_data'],
                    $filterData['sql_operators'],
                    $filterData['and_or_operators']
                );
            }

            if (isset($filterData['order_by'])) {
                $booksResult = $booksResult->orderBy($filterData['order_by'], $filterData['order_by_type']);
            }

            $booksResult = $booksResult->query()->fetchAll();
            if (!$booksResult) {
                $result['books'] = null;
                $this->msgModel->setMessage('empty', 'filter_catalog', 'empty_catalog');
            } else {
                $result['books'] = $booksResult;
            }
        } else {
            $result['books'] = $booksResult;
        }

        return $result;
    }

    protected function getCatalogFields(): ?array
    {
        return $this->catalogFields;
    }
}
