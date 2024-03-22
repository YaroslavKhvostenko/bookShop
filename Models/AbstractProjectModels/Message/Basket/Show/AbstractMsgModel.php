<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Basket\Show;

use Models\AbstractProjectModels\Message\Basket\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_BASKET_MSG = 'На данный момент ваша корзина пуста! Добавте книги из каталога в корзину!';
    private const EMPTY_DATA = [
        'empty_basket' => self::EMPTY_BASKET_MSG
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
    }
}
