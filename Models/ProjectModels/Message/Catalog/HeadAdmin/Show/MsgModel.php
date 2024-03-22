<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Catalog\HeadAdmin\Show;

use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_CATALOG = 'На данный момент нет ни одной книги в базе данных! 
    Вам следует выполнить несколько действий : Дать пинок под зад менеджеру по закупкам, 
    чтоб он предоставил вам список -> дать администраторам(рабам=))) задание, добавить книги в базу данных!';
    private const EMPTY_DATA = [
        'catalog' => self::EMPTY_CATALOG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages['empty'] = array_merge($this->messages['empty'], self::EMPTY_DATA);
    }
}
