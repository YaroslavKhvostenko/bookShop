<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Catalog\Admin\Show;

use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_CATALOG = 'Нет книг добавленных в базу данных! Вам скоро поручат их добавить!)))';
    private const EMPTY_DATA = [
        'catalog' => self::EMPTY_CATALOG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages['empty'] = array_merge($this->messages['empty'], self::EMPTY_DATA);
    }
}
