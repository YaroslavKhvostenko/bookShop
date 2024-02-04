<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\Add;

use Models\AbstractProjectModels\Message\User\Add\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    protected const PHONE = 'phone';
    protected const ADDRESS = 'address';
    private const PHONE_EMPTY_MSG = 'Вы забыли указать телефон!';
    private const ADDRESS_EMPTY_MSG = 'Вы забыли указать аддресс!';
    private const PHONE_WRONG_MSG = 'Неправильный формат телефона!';
    private const ADDRESS_WRONG_MSG = 'Неправильный формат адресса!';
    private const PHONE_SUCCESS_MSG = 'Ваш телефон был успешно добавлен!';
    private const ADDRESS_SUCCESS_MSG = 'Ваш адресс был успешно добавлен!';
    private const PHONE_FAILURE_MSG = 'Невозможно сохранить этот номер телефона! Попробуйте другой!';
    private const EMPTY_DATA = [
        self::PHONE => self::PHONE_EMPTY_MSG,
        self::ADDRESS => self::ADDRESS_EMPTY_MSG
    ];
    private const WRONG_DATA = [
        self::PHONE => self::PHONE_WRONG_MSG,
        self::ADDRESS => self::ADDRESS_WRONG_MSG
    ];
    private const SUCCESS_MSGS = [
        self::PHONE => self::PHONE_SUCCESS_MSG,
        self::ADDRESS => self::ADDRESS_SUCCESS_MSG
    ];
    private const FAILURE_MSGS = [
        self::PHONE => self::PHONE_FAILURE_MSG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages[self::EMPTY] = array_merge($this->messages[self::EMPTY], self::EMPTY_DATA);
        $this->messages[self::WRONG] = array_merge($this->messages[self::WRONG], self::WRONG_DATA);
        $this->messages[self::SUCCESS_RESULT] = array_merge($this->messages[self::SUCCESS_RESULT], self::SUCCESS_MSGS);
        $this->messages[self::FAILURE_RESULT] = self::FAILURE_MSGS;
    }
}
