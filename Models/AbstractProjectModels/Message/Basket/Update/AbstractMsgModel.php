<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Basket\Update;

use Models\AbstractProjectModels\Message\Basket\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const SUCCESS_UPDATE_MSG = 'Количество товаров в корзине было успешно изменено!';
    private const EMPTY_UPDATE_DATA = 'Вы не вписали ни одно числовое изменение ни для одной книги!';
    private const ZERO_QUANTITY_WRONG_MSG = 'Какой смысл писать 0 !? Уберите товар с помощью кнопки \'Убрать из корзины\'';
    private const SELF_QUANTITY_WRONG_MSG = 'Вы вписали то же самое количество для товара что уже стоит!';
    private const FAILURE_BIG_QUANTITY_MSG = 'К сожалению у нас нет такого количества книг! Измените на меньшее число!';
    private const EMPTY_DATA = [
        'empty_data' => self::EMPTY_UPDATE_DATA
    ];
    private const WRONG_DATA = [
        'zero_quantity' => self::ZERO_QUANTITY_WRONG_MSG,
        'self_quantity' => self::SELF_QUANTITY_WRONG_MSG
    ];
    private const FAILURE_MSGS = [
        'big_quantity' => self::FAILURE_BIG_QUANTITY_MSG
    ];
    private const SUCCESS_MSGS = [
        'success_update' => self::SUCCESS_UPDATE_MSG
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
        $this->messages['wrong'] = self::WRONG_DATA;
        $this->messages['failure'] = self::FAILURE_MSGS;
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
