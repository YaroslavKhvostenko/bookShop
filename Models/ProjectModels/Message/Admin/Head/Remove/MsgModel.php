<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin\Head\Remove;

use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const ADMIN_DOES_NOT_EXIST = 'На данный момент у вас в проэкте нет администраторов с правами доступа!';
    private const EMPTY_DATA_MSG = 'Вы забыли выбрать администратора для снятия доступа!';
    private const REMOVE_SUCCESS_MSG = 'Вы успешно отобрали допуск!';
    private const EMPTY_DATA = [
        self::NO_ADMINS => self::ADMIN_DOES_NOT_EXIST,
        'empty_data' => self::EMPTY_DATA_MSG
    ];
    private const SUCCESS_MSGS = [
        'remove' => self::REMOVE_SUCCESS_MSG
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
    }
}
