<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Filter\Clear;

use Models\AbstractProjectModels\Message\Filter\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_CLEAR_FILTER_CHECK_MARK = 'Вы забыли поставить галочку для подтверждения очистки фильтра!';
    private const SUCCESS_CLEAR_FILTER = 'Фильтр был успешно очищен!';
    private const EMPTY_DATA = [
        'empty_clear_filter_check_mark' => self::EMPTY_CLEAR_FILTER_CHECK_MARK
    ];
    private const SUCCESS_MSGS = [
        'success_clear_filter' => self::SUCCESS_CLEAR_FILTER
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
