<?php
declare(strict_types=1);

namespace Models\ProjectModels\Cookies\Models\Basket;

use Models\ProjectModels\Cookies\Manager;

class CookieModel
{
    private static CookieModel $selfInstance;
    private Manager $cookiesInfo;
    private ?array $data;


    private function __construct()
    {
        $this->cookiesInfo = Manager::getInstance();
        $this->data = $this->unserializeData($this->cookiesInfo->getData('basket'));
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    public static function getInstance(): CookieModel
    {
        return static::createSelf();
    }

    protected static function createSelf(): CookieModel
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }

    private function unserializeData(?string $cookieData): ?array
    {
        return !is_null($cookieData) ? unserialize(base64_decode($cookieData)) : null;
    }

    private function serializeData(array $data): string
    {
        return base64_encode(serialize($data));
    }

    private function setBasketData(): void
    {
        $this->cookiesInfo->setData('basket', $this->serializeData($this->data), 1200);
    }

    public function productItemExist(int $productId): bool
    {
        return isset($this->data[$productId]);
    }

    public function setProductItem(int $productId, array $productData): void
    {
        $this->data[$productId] = $productData;
        $this->setBasketData();
    }

    public function isEmptyBasket(): bool
    {
        return empty($this->data);
    }

    public function getBasketData(): ?array
    {
        return $this->data;
    }

    public function clearBasket(): void
    {
        $this->cookiesInfo->unsetData('basket', $this->serializeData($this->data), 1200);
    }

    public function updateBasket(array $updateData): void
    {
        foreach ($updateData as $productId => $quantityDetails) {
            $this->data[$productId]['quantity'] = (int)$quantityDetails['quantity'];
        }
        $this->setBasketData();
    }

    public function removeProduct(int $productId): void
    {
        unset($this->data[$productId]);
        $this->setBasketData();
    }
}
