<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin\Admin\Task;

use Models\AbstractProjectModels\Message\Admin\Task\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_TASKS = 'На данный момент у вас нет задач!';
    private const NO_COMPLETED_TASKS = 'На данный момент у вас нет выполненных задач!';
    private const NO_UNFINISHED_TASKS = 'На данный момент у вас нет не выполненных задач!';
    private const EMPTY_DATA = [
        'empty_tasks' => self::EMPTY_TASKS,
        'completed_tasks' => self::NO_COMPLETED_TASKS,
        'unfinished_tasks' => self::NO_UNFINISHED_TASKS
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = array_merge($this->messages[self::EMPTY], self::EMPTY_DATA);
    }
}
