<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Filter\Admin\Set;

use Models\AbstractProjectModels\Message\Filter\Set\AbstractMsgModel as BaseMsgModel;

abstract class AbstractMsgModel extends BaseMsgModel
{
    private const EMPTY_FILTER_TASK = 'Вы забыли выбрать параметры для фильрации задач!';
    private const EMPTY_DATA = [
        'empty_filter_task' => self::EMPTY_FILTER_TASK
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages['empty'] = array_merge($this->messages['empty'], self::EMPTY_DATA);
    }
}
