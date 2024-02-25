<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\GuestAdmin\Registration;

use Models\AbstractProjectModels\Message\User\Guest\Registration\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    protected const ADMIN_PASS = 'admin_pass';
    protected const PHONE = 'phone';
    protected const ADDRESS = 'address';
    private const ADMIN_PASS_EMPTY_MSG = 'Вы забыли указать админовский пароль!';
    private const PHONE_EMPTY_MSG = 'Вы забыли указать телефон!';
    private const ADDRESS_EMPTY_MSG = 'Вы забыли указать аддресс!';
    private const ADMIN_PASS_WRONG_MSG = 'Неправильный пароль админа!';
    private const EMPTY_DATA = [
        self::ADMIN_PASS => self::ADMIN_PASS_EMPTY_MSG,
        self::PHONE => self::PHONE_EMPTY_MSG,
        self::ADDRESS => self::ADDRESS_EMPTY_MSG
    ];
    private const WRONG_DATA = [
        self::ADMIN_PASS => self::ADMIN_PASS_WRONG_MSG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages[self::EMPTY] = array_merge($this->messages[self::EMPTY], self::EMPTY_DATA);
        $this->messages[self::WRONG] = array_merge($this->messages[self::WRONG], self::WRONG_DATA);
    }
}
