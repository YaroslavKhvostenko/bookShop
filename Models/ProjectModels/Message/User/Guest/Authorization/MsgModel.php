<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\Guest\Authorization;

use Models\AbstractProjectModels\Message\User\Guest\Authorization\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const SUCCESS_AUTHORIZATION_MSG = 'Добро пожаловать в личный кабинет наш любимый пользователь!';
    private const SUCCESS_MSGS = [
        self::SUCCESS_AUTHORIZATION => self::SUCCESS_AUTHORIZATION_MSG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
    }
}
