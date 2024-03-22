<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Basket\Remove;

use Models\AbstractProjectModels\Message\Basket\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const SUCCESS_PRODUCT_REMOVE_MSG = 'Товар был успешно удален из корзины!';
    private const SUCCESS_MSGS = [
        'product_remove' => self::SUCCESS_PRODUCT_REMOVE_MSG
    ];

    public function __construct()
    {
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
