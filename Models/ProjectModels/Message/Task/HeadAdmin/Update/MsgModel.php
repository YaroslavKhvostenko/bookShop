<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Task\HeadAdmin\Update;

use Models\AbstractProjectModels\Message\Task\Update\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_ADMIN = 'Вы забыли выбрать администратора!';
    private const SUCCESS_TASK = 'Вы успешно переназначили ответсвтенного за задачу!';
    private const EMPTY_DATA = [
        'empty_admin' => self::EMPTY_ADMIN
    ];
    private const SUCCESS_MSGS = [
        'task' => self::SUCCESS_TASK
    ];

    public function __construct()
    {
        $this->messages['empty'] = array_merge($this->messages['empty'], self::EMPTY_DATA);
        $this->messages['success'] = array_merge($this->messages['success'], self::SUCCESS_MSGS);
    }
}
