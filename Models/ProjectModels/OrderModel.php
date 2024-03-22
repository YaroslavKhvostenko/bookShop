<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\AbstractOrderModel;
use Models\ProjectModels\Session\User\SessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

class OrderModel extends AbstractOrderModel
{
    private ?BasketModel $basketModel = null;
    protected ?MySqlDbWorkModel $db = null;

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    public function isEmptyBasket(): bool
    {
        return $this->getBasketModel()->isEmptyBasket();
    }

    private function getBasketModel(): BasketModel
    {
        if (!$this->basketModel) {
            $this->basketModel = new BasketModel();
        }

        return $this->basketModel;
    }

    public function newOrder(array $orderData): bool
    {
        $basketData = $this->getBasketModel()->getBasketData();
        if ($this->basketModel->compareWithDb($basketData)) {
            if ($this->userSessionModel->isLoggedIn()) {
                $orderData = $this->setCustomerId($orderData);
            }

            $updateRequestData = $this->formatProductUpdateQuantity($basketData);
            try {
                $this->getDb()->beginTransaction();
                foreach ($updateRequestData as $productUpdateRequestData) {
                    $this->db->update(
                        ['books_catalog'],
                        $productUpdateRequestData['update_data']
                    )->condition($productUpdateRequestData['condition_data'])->exec();
                }

                $this->db->insertData('orders', $orderData);
                $orderId = (int)$this->db->getLastInsertedId();
                $orderItemsData = [];
                foreach ($basketData as $productId => $productDetails) {
                    $productId = (int)$productId;
                    $orderItemsData[$productId]['order_id'] = $orderId;
                    $orderItemsData[$productId]['book_id'] = $productId;
                    $orderItemsData[$productId]['book_order_quantity'] = (int)$productDetails['quantity'];
                }

                foreach ($orderItemsData as $orderItemData) {
                    $this->db->insertData('order_item', $orderItemData);
                }

                $this->getDb()->commit();
                $this->basketModel->clearBasket();
                $this->msgModel->setMessage('success', 'new_order', 'success_new_order');
            } catch (\PDOException $exception) {
                $this->db->rollBack();
                $this->msgModel->setErrorMsg();
            }
        } else {
            $this->msgModel->setMessage('failure', 'big_quantity', 'big_quantity');

            return false;
        }

        return true;
    }

    private function setCustomerId(array $customerData): ?array
    {
        $customerId = $this->userSessionModel->getCustomerData()['id'] ?? null;
        if (is_null($customerId)) {
            throw new \Exception(
                'Didn\'t find customer id in session customer data!'
            );
        }

        $conditionData['id'] = $customerId;
        $dbResult = $this->getDb()->select(['id'])->from(['users'])->condition($conditionData)->query()->fetch();
        if (!$dbResult) {
            throw new \Exception(
                'Didn\'t find customer id in DB, using customer id from session customer data!'
            );
        }

        $customerData['customer_id'] = (int)$customerId;

        return $customerData;
    }

    protected function getDb(): MySqlDbWorkModel
    {
        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
            $this->db->setMessageModel($this->msgModel);
        }

        return $this->db;
    }

    private function formatProductUpdateQuantity(array $basketData): array
    {
        $countBasketData = count($basketData);
        $conditionData = [];
        if ($countBasketData === 1) {
            $conditionData['id'] = (int)array_key_first($basketData);
        } else {
            foreach ($basketData as $productId => $productDetails) {
                $conditionData['id'][] = (int)$productId;
            }
        }
        $dbResult = $this->getDb()->select(['id', 'quantity'])->
            from(['books_catalog'])->
            condition($conditionData)->
            query()->fetchAll();
        if (count($dbResult) !== $countBasketData) {
            throw new \Exception(
                'Different number of products from basket data and db result data!'
            );
        }

        $updateRequestData = [];
        foreach ($dbResult as $item) {
            $itemData = [];
            $itemData['condition_data']['id'] = (int)$item['id'];
            $quantity = (int)$item['quantity'] - (int)$basketData[(int)$item['id']]['quantity'];
            if ($quantity < 0) {
                throw new \Exception(
                    'Quantity of product from basket bigger then quantity of product from DB!'
                );
            }

            $itemData['update_data']['quantity'] = $quantity;
            $updateRequestData[] = $itemData;
        }


        return $updateRequestData;
    }
}
