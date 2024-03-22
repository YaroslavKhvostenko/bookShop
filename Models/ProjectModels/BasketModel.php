<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Cookies\Models\Basket\CookieModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

class BasketModel
{
    private ?AbstractBaseMsgModel $msgModel = null;
    private CookieModel $basketCookieModel;
    protected ?MySqlDbWorkModel $db = null;

    public function __construct()
    {
        $this->basketCookieModel = CookieModel::getInstance();
    }

    public function addItem(array $productData): void
    {
        $productId = array_key_first($productData);
        if (!$this->basketCookieModel->productItemExist($productId)) {
            $this->basketCookieModel->setProductItem($productId ,$productData[$productId]);
            $this->msgModel->setMessage('success', 'product_add', 'success_product_add');
        } else {
            $this->msgModel->setMessage('failure', 'product_add', 'failure_product_add');
        }
    }


    public function getBasket(): array
    {
        $result = [];
        $basketData = $this->getBasketData();
        $countBasketProducts = count($basketData);
        if ($countBasketProducts === 1) {
            $conditionData['id'] = array_key_first($basketData);
        } else {
            foreach ($basketData as $productId => $productDetails) {
                $conditionData['id'][] = $productId;
            }
        }

        $selectFields = [
            'id',
            'title',
            'image',
            'price'
        ];
        $dbResult = $this->getDb()->select($selectFields)->
        from(['books_catalog'])->
        condition($conditionData)->
        query()->
        fetchAll();
        if ($countBasketProducts !== count($dbResult)) {
            throw new \Exception(
                'Different quantity of $countBasketProducts and count($dbResult)!'
            );
        }

        foreach ($basketData as $productId => $productDetails) {
            foreach ($dbResult as $itemDetails) {
                if ($productId === (int)$itemDetails['id']) {
                    $result[$productId] = array_merge($productDetails, $itemDetails);
                    unset($result[$productId]['id']);
                }
            }
        }

        return $result;
    }

    public function clearBasket(): void
    {
        if ($this->isEmptyBasket()) {
            throw new \Exception(
                'Trying to clear basket, but basket is empty!'
            );
        }

        $this->basketCookieModel->clearBasket();
    }

    public function updateBasket(array $updateData)
    {
        if ($this->isEmptyBasket()) {
            throw new \Exception(
                'Trying to update data in EMPTY basket!!!'
            );
        }

        foreach ($updateData as $productId => $quantityDetails) {
            if (!$this->basketCookieModel->productItemExist($productId)) {
                throw new \Exception(
                    'Trying to update product in basket, but product doesn\'t exist in basket!'
                );
            }

            if ($quantityDetails['quantity'] === $this->getBasketData()[$productId]['quantity']) {
                $this->msgModel->setMessage('wrong', 'self_quantity', 'self_quantity');

                return;
            }
        }

        if ($this->compareWithDb($updateData)) {
            $this->basketCookieModel->updateBasket($updateData);
            $this->msgModel->setMessage('success', 'success_update', 'success_update');
        } else {
            $this->msgModel->setMessage('failure', 'big_quantity', 'big_quantity');
        }
    }

    public function removeItem(int $productId): void
    {
        if ($this->isEmptyBasket()) {
            throw new \Exception(
                'Problems with removing product from basket, because basket is empty!!!'
            );
        }

        if (!$this->basketCookieModel->productItemExist($productId)) {
            throw new \Exception(
                'Problems with removing product from basket, because basket doesn\'t have this product id!'
            );
        }

        $this->basketCookieModel->removeProduct($productId);
        $this->msgModel->setMessage('success', 'product_remove', 'success_product_remove');
    }

    public function setMessageModel(AbstractBaseMsgModel $msgModel): void
    {
        if (!$this->msgModel) {
            $this->msgModel = $msgModel;
        }
    }

    private function getDb(): MySqlDbWorkModel
    {
        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
            if ($this->msgModel) {
                $this->db->setMessageModel($this->msgModel);
            }
        }

        return $this->db;
    }

    public function isEmptyBasket(): bool
    {
        return $this->basketCookieModel->isEmptyBasket();
    }

    public function compareWithDb(array $checkData): bool
    {
        $countCheckData = count($checkData);
        if ($countCheckData === 1) {
            $conditionData['id'] = array_key_first($checkData);
        } else {
            foreach ($checkData as $productId => $quantityDetails) {
                $conditionData['id'][] = $productId;
            }
        }

        $dbResult = $this->getDb()->select(['id', 'quantity'])->
        from(['books_catalog'])->
        condition($conditionData)->
        query()->fetchAll();
        if ($countCheckData !== count($dbResult)) {
            throw new \Exception(
                'Didn\'t find product id in DB!'
            );
        } else {
            $dbData = [];
            foreach ($dbResult as $item) {
                $dbData[(int)$item['id']]['quantity'] = (int)$item['quantity'];
            }

            foreach ($checkData as $productId => $productDetails) {
                if ($productDetails['quantity'] > $dbData[$productId]['quantity']) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getBasketData(): ?array
    {
        return $this->basketCookieModel->getBasketData();
    }
}
