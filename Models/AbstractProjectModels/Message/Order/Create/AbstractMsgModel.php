<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Order\Create;

use Models\AbstractProjectModels\Message\Order\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_BASKET_MSG = 'Ваша корзина пуста! Вы не можете оформить заказ!';
    private const EMPTY_CUSTOMER_NAME = 'Вы забыли указать свое имя!';
    private const EMPTY_CUSTOMER_EMAIL = 'Вы забыли указать свою почту!';
    private const EMPTY_CUSTOMER_PHONE = 'Вы забыли указать свой телефон!';
    private const EMPTY_CUSTOMER_ADDRESS = 'Вы забыли указать аддресс доставки!';
    private const WRONG_CUSTOMER_NAME = 'Неправильный формат имени!';
    private const WRONG_CUSTOMER_EMAIL = 'Неправильный формат почты!';
    private const WRONG_CUSTOMER_PHONE = 'Неправильный формат телефона!';
    private const WRONG_CUSTOMER_ADDRESS = 'Неправильный формат аддресса доставки!';
    private const WRONG_ORDER_COMMENT = 'Неправильный формат комментария к заказу!';
    private const BIG_QUANTITY_FAILURE_MSG = 'Слишком большое количество для какойто из книг!';
    private const SUCCESS_NEW_ORDER = 'Вы успешно оформили заказ!';
    private const EMPTY_DATA = [
        'empty_basket' => self::EMPTY_BASKET_MSG,
        'customer_name' => self::EMPTY_CUSTOMER_NAME,
        'customer_email' => self::EMPTY_CUSTOMER_EMAIL,
        'customer_phone' => self::EMPTY_CUSTOMER_PHONE,
        'customer_address' => self::EMPTY_CUSTOMER_ADDRESS,
    ];
    private const WRONG_DATA = [
        'customer_name' => self::WRONG_CUSTOMER_NAME,
        'customer_email' => self::WRONG_CUSTOMER_EMAIL,
        'customer_phone' => self::WRONG_CUSTOMER_PHONE,
        'customer_address' => self::WRONG_CUSTOMER_ADDRESS,
        'order_comment' => self::WRONG_ORDER_COMMENT,
    ];
    private const FAILURE_MSGS = [
        'big_quantity' => self::BIG_QUANTITY_FAILURE_MSG
    ];
    private const SUCCESS_MSGS = [
        'new_order' => self::SUCCESS_NEW_ORDER
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
        $this->messages['wrong'] = self::WRONG_DATA;
        $this->messages['failure'] = self::FAILURE_MSGS;
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
