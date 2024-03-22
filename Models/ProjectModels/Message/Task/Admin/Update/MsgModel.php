<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Task\Admin\Update;

use Models\AbstractProjectModels\Message\Task\Update\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_TASKS = 'На данный момент нет ни одной задачи для вас!';
    private const EMPTY_TASK = 'Вы забрыли выбрать задачу!';
    private const SUCCESS_TASK = 'Поздравляем с успешным выполнение задачи и изменением ее статуса!';
    private const EMPTY_DATA = [
        'empty_tasks' => self::EMPTY_TASKS,
        'empty_task' => self::EMPTY_TASK,
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
