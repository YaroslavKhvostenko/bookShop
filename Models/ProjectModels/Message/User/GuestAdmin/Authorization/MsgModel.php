<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\GuestAdmin\Authorization;

use Models\AbstractProjectModels\Message\User\Guest\Authorization\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    protected const ADMIN_PASS = 'admin_pass';
    private const ADMIN_PASS_EMPTY_MSG = 'Вы забыли указать админовский пароль!';
    private const SUCCESS_AUTHORIZATION_MSG = 'Добро пожаловать в личный кабинет о великий и ужасный Админ!';
    private const ADMIN_PASS_FAILURE_MSG = 'Неправильный пароль админа!';
    private const EMPTY_DATA = [
        self::ADMIN_PASS => self::ADMIN_PASS_EMPTY_MSG
    ];
    private const SUCCESS_MSGS = [
        self::SUCCESS_AUTHORIZATION => self::SUCCESS_AUTHORIZATION_MSG
    ];
    private const FAILURE_MSGS = [
        self::ADMIN_PASS => self::ADMIN_PASS_FAILURE_MSG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages[self::EMPTY] = array_merge($this->messages[self::EMPTY], self::EMPTY_DATA);
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
        $this->messages[self::FAILURE_RESULT] = array_merge($this->messages[self::FAILURE_RESULT], self::FAILURE_MSGS);
    }
}
