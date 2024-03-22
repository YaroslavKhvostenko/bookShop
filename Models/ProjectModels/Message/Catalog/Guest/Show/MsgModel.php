<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Catalog\Guest\Show;

use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_CATALOG = 'К огромному сожалению на данный момент в нашем магазине нет книг для продажи!';
    private const EMPTY_DATA = [
        'catalog' => self::EMPTY_CATALOG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages['empty'] = array_merge($this->messages['empty'], self::EMPTY_DATA);
    }
}

