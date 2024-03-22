<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Catalog;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel as BaseMessageModel;

abstract class AbstractBaseMsgModel extends BaseMessageModel
{
    private const EMPTY_CATALOG = 'Ни одной книги не найдено при данной фильтрации!';
    private const EMPTY_DATA = [
        'filter_catalog' => self::EMPTY_CATALOG
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
    }
}
