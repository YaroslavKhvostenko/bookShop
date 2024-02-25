<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\User\Guest\Authorization;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    protected const LOGIN = 'login';
    protected const PASS = 'pass';
    private const LOGIN_EMPTY_MSG = 'Вы забыли указать ваш логин!';
    private const PASS_EMPTY_MSG = 'Вы забыли указать ваш пароль!';
    protected const SUCCESS_AUTHORIZATION = self::SUCCESS_RESULT . '_authorization';
    protected const NOT_ACTIVE = 'not_active';
    private const USER_NOT_EXIST_MSG = 'Такого пользователя не существует!';
    private const PASS_FAILURE_MSG = 'Неправильный пароль!';
    private const NOT_ACTIVE_MSG = 'Страница была удалена! Не желаете ли восстановить!?';
    private const EMPTY_DATA = [
        self::LOGIN => self::LOGIN_EMPTY_MSG,
        self::PASS => self::PASS_EMPTY_MSG
    ];
    private const FAILURE_MSGS = [
        self::USER_ESSENCE => self::USER_NOT_EXIST_MSG,
        self::PASS => self::PASS_FAILURE_MSG,
        self::NOT_ACTIVE => self::NOT_ACTIVE_MSG
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::FAILURE_RESULT] = self::FAILURE_MSGS;
    }
}
