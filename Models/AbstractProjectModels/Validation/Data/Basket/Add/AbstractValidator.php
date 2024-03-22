<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Basket\Add;

use Models\AbstractProjectModels\Validation\Data\Basket\AbstractValidator as BaseValidator;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractValidator extends BaseValidator
{
    protected ?int $productId = null;
    protected ?array $productResult = null;
    protected ?MySqlDbWorkModel $db = null;

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function validateParams(array $data): ?array
    {
        if (count($data) !== 1) {
            throw new \Exception(
                'Wrong quantity of params from URI string!'
            );
        }

        return $this->validateProduct($data);
    }

    /**
     * @param string $productId
     * @return int
     * @throws \Exception
     */
    protected function validateProductId(string $productId): int
    {
        if (!is_numeric($productId)) {
            throw new \Exception(
                'Product id must be numeric! Probably you sent word from the html form!'
            );
        }

        return (int)$productId;
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function validateProduct(array $data): ?array
    {
        $this->productId = $this->validateProductId($data[0]);
        $this->checkProductInDb();
        $this->formatProductResult();

        return $this->productResult;
    }

    /**
     * @throws \Exception
     */
    protected function checkProductInDb(): void
    {
        $dbResult = $this->getDb()->select(['id'])->
            from(['books_catalog'])->
            condition(['id' => $this->productId])->
            query()->fetch();
        if (!$dbResult) {
            throw new \Exception(
                'Didnt find book in `books_catalog` with id = ' . "$this->productId !"
            );
        }
    }

    protected function formatProductResult(): void
    {
        $this->productResult[$this->productId] = [
            'quantity' => 1
        ];
    }

    protected function getDb(): MySqlDbWorkModel
    {
        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
        }

        return $this->db;
    }
}
