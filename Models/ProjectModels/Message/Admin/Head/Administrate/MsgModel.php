<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Admin\Head\Administrate;

use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const ADMIN_DOES_NOT_EXIST = 'На данный момент у вас в проэкте нет администраторов!';
    private const EMPTY_DATA = [
        self::NO_ADMINS => self::ADMIN_DOES_NOT_EXIST
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
    }
}
