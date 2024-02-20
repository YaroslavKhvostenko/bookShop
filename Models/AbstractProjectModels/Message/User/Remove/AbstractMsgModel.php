<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\User\Remove;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    protected const IMAGE = 'image';
    private const IMAGE_SUCCESS_MSG = 'Ваш аватар был успешно удалён!';
    private const SUCCESS_MSGS = [
        self::IMAGE => self::IMAGE_SUCCESS_MSG
    ];

    public function __construct()
    {
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
    }
}
