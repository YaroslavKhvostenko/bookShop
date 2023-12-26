<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\Remove;

use Models\AbstractProjectModels\Message\User\Remove\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    protected const PHONE = 'phone';
    protected const ADDRESS = 'address';
    private const PHONE_SUCCESS_MSG = 'Ваш телефон был успешно удалён!';
    private const ADDRESS_SUCCESS_MSG = 'Ваш адресс был успешно удалён!';
    private const SUCCESS_MSGS = [
        self::PHONE => self::PHONE_SUCCESS_MSG,
        self::ADDRESS => self::ADDRESS_SUCCESS_MSG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages[self::SUCCESS_RESULT] = array_merge($this->messages[self::SUCCESS_RESULT], self::SUCCESS_MSGS);
    }
}
