<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin\Head\Task;

use Models\AbstractProjectModels\Message\Admin\Task\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_TASKS = 'На данный момент задач нет!';
    private const EMPTY_DATA = [
        'empty_tasks' => self::EMPTY_TASKS
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = array_merge($this->messages[self::EMPTY], self::EMPTY_DATA);
    }
}
