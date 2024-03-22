<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Basket\Add;

use Models\AbstractProjectModels\Message\Basket\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const SUCCESS_PRODUCT_ADD_MSG = 'Товар был успешно давлен в корзину!';
    private const FAILURE_PRODUCT_ADD_MSG = 'Товар был добален в корзину ранее!';
    private const FAILURE_MSGS = [
        'product_add' => self::FAILURE_PRODUCT_ADD_MSG
    ];
    private const SUCCESS_MSGS = [
        'product_add' => self::SUCCESS_PRODUCT_ADD_MSG
    ];

    public function __construct()
    {
        $this->messages['failure'] = self::FAILURE_MSGS;
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
