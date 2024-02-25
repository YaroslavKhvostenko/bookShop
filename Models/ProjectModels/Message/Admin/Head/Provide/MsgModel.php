<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin\Head\Provide;

use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const ADMINS_EMPTY = 'На данный момент нет администраторов без прав доступа!';
    private const EMPTY_DATA_MSG = 'Вы забыли выбрать админа для предоставления доступа!';
    private const PROVIDE_SUCCESS_MSG = 'Вы успешно предоставили допуск!';
    private const EMPTY_DATA = [
        self::NO_ADMINS => self::ADMINS_EMPTY,
        'empty_data' => self::EMPTY_DATA_MSG
    ];
    private const SUCCESS_MSGS = [
        'provide' => self::PROVIDE_SUCCESS_MSG
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
    }
}
