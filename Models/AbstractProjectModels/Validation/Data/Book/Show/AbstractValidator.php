<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Book\Show;

use Models\AbstractProjectModels\Validation\Data\Book\AbstractValidator as BaseValidator;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractValidator extends BaseValidator
{
    protected ?MySqlDbWorkModel $db = null;

    /**
     * @param array $uriParams
     * @return int|null
     * @throws \Exception
     */
    public function validateParams(array $uriParams): ?int
    {
        if (count($uriParams) !== 1) {
            throw new \Exception(
                'Only one param should be from uri string!'
            );
        }

        $productId = $uriParams[0];
        if (!is_numeric($productId)) {
            throw new \Exception(
                'Property $productId must be numeric, you received : ' . "'$productId'" . '!'
            );
        } else {
            $productId = (int)$productId;
        }

        return $this->checkProductInDb($productId);
    }

    /**
     * @param int $productId
     * @return int
     * @throws \Exception
     */
    protected function checkProductInDb(int $productId): int
    {
        $dbResult = $this->getDb()->select(['id'])
            ->from(['books_catalog'])->
            condition(['id' => $productId])->
            query()->fetch();

        if (!$dbResult) {
            throw new \Exception(
                'Didn\'t find product id in DB table `books_catalog`!'
            );
        }

        return $productId;
    }

    protected function getDb(): MySqlDbWorkModel
    {
        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
        }

        return $this->db;
    }
}
