<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Basket\Clear;

use Models\AbstractProjectModels\Message\Basket\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const SUCCESS_CLEAR_BASKET_MSG = 'Корзина была успешно очищена!';
    private const SUCCESS_MSGS = [
        'clear_basket' => self::SUCCESS_CLEAR_BASKET_MSG
    ];

    public function __construct()
    {
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
