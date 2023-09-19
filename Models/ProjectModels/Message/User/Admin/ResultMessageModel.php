<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\Admin;

use Models\AbstractProjectModels\Message\User\AbstractMsgModel;

class ResultMessageModel extends AbstractMsgModel
{
    private const ADMIN_PASS = 'admin_pass';
    private const ADMIN_PASS_MSG = 'Неправильный пароль админа!';
    private const EMPTY_DATA = [
        'admin_pass' => 'Вы забыли указать админовский пароль!',
        'phone' => 'Вы забыли указать телефон!',
        'address' => 'Вы забыли указать аддресс!'
    ];
    private const WRONG_DATA = [
        self::ADMIN_PASS => self::ADMIN_PASS_MSG
    ];
    private const LOGIN_RESULT = [
        self::ADMIN_PASS => self::ADMIN_PASS_MSG
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = array_merge($this->messages[self::EMPTY], self::EMPTY_DATA);
        $this->messages[self::WRONG] = array_merge($this->messages[self::WRONG], self::WRONG_DATA);
        $this->messages[self::LOGIN] = array_merge($this->messages[self::LOGIN], self::LOGIN_RESULT);
        parent::__construct();
    }
}
