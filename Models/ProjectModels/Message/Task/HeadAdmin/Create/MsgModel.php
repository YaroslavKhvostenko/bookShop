<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Task\HeadAdmin\Create;

use Models\AbstractProjectModels\Message\Task\Create\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_ADMINS = 'На данный момент нет администраторов с доступом, поэтому вы не можете создать задачу!';
    private const EMPTY_ADMIN = 'Вы не выбрали ни одного администратора! Выберите пожалуйста!';
    private const EMPTY_DESCRIPTION = 'Вы не написали описание задачи!';
    private const WRONG_DESCRIPTION = 'Не правильный формат описания задачи!';
    private const SUCCESS_TASK = 'Вы успешно добавили новую задачу администратору!';
    private const EMPTY_DATA = [
        'empty_admins' => self::EMPTY_ADMINS,
        'admin_id' => self::EMPTY_ADMIN,
        'task_description' => self::EMPTY_DESCRIPTION
    ];
    private const WRONG_DATA = [
        'task_description' => self::WRONG_DESCRIPTION
    ];
    private const SUCCESS_MSGS = [
        'task' => self::SUCCESS_TASK
    ];

    public function __construct()
    {
        $this->messages['empty'] = array_merge($this->messages['empty'], self::EMPTY_DATA);
        $this->messages['wrong'] = array_merge($this->messages['wrong'], self::WRONG_DATA);
        $this->messages['success'] = array_merge($this->messages['success'], self::SUCCESS_MSGS);
    }
}
