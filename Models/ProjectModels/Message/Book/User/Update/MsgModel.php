<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Book\User\Update;

use Models\AbstractProjectModels\Message\Book\Update\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_RATING_MSG = 'Вы забыли написать новую оценку для книги!';
    private const WRONG_RATING_MSG = 'Неправильный формат оценки книги! Следуйте рекомендациям!';
    private const FAILURE_SELF_RATING_MSG = 'Вы поставили ту же оценку что и была!';
    private const SUCCESS_RATING_MSG = 'Оценка была успешно изменена!';
    private const EMPTY_DATA = [
        'rating' => self::EMPTY_RATING_MSG
    ];
    private const WRONG_DATA = [
        'rating' => self::WRONG_RATING_MSG,
    ];
    private const FAILURE_MSGS = [
        'self_rating' => self::FAILURE_SELF_RATING_MSG
    ];
    private const SUCCESS_MSGS = [
        'rating' => self::SUCCESS_RATING_MSG,
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
        $this->messages['wrong'] = self::WRONG_DATA;
        $this->messages['success'] = self::SUCCESS_MSGS;
        $this->messages['failure'] = self::FAILURE_MSGS;
    }
}
