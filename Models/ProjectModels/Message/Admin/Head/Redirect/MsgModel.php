<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin\Head\Redirect;

use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const ADMIN_DOES_NOT_EXIST = 'На данный момент у вас в проэкте нет администраторов с допуском, 
                                                                            для передачи должности главного админа!';
    private const EMPTY_DATA_MSG = 'Вы забыли выбрать администратора для передачи должности!';
    private const REDIRECT_SUCCESS_MSG = 'Вы успешно передали должность!';
    private const TOO_MUCH_ADMINS = 'Вы можете передать должность Главного Аминистратора,
                                                                                    только одному администратору!';
    private const EMPTY_DATA = [
        self::NO_ADMINS => self::ADMIN_DOES_NOT_EXIST,
        'empty_data' => self::EMPTY_DATA_MSG
    ];
    private const SUCCESS_MSGS = [
        'redirect' => self::REDIRECT_SUCCESS_MSG
    ];
    private const FAILURE_MSGS = [
        'too_much_admins' =>  self::TOO_MUCH_ADMINS
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
        $this->messages[self::FAILURE_RESULT] = self::FAILURE_MSGS;
    }
}
