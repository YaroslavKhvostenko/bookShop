<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Book\Guest\Add;

use Models\AbstractProjectModels\Message\Book\Add\AbstractMsgModel;

class MsgModel extends AbstractMsgModel
{
    private const EMPTY_RATING_MSG = 'Вы забыли написать оценку для книги!';
    private const EMPTY_COMMENT_MSG = 'Вы забыли написать комментарий для книги!';
    private const EMPTY_COMMENT_AUTHOR_NAME_MSG = 'Вы забыли написать свой псевдоним!';
    private const WRONG_RATING_MSG = 'Неправильный формат оценки книги! Следуйте рекомендациям!';
    private const WRONG_COMMENT_MSG = 'Неправильный формат комментария!';
    private const BAD_COMMENT_MSG = 'Нельзя использовать нецензурную лексику в комментариях!';
    private const WRONG_AUTHOR_NAME_MSG = 'Неправильный формат псевдонима! Следуйте рекомендациям!';
    private const SUCCESS_RATING_MSG = 'Оценка была успешно добавлена!';
    private const SUCCESS_COMMENT_MSG = 'Комментарий был успешно добавлен!';
    private const EMPTY_DATA = [
        'rating' => self::EMPTY_RATING_MSG,
        'comment' => self::EMPTY_COMMENT_MSG,
        'author_name' => self::EMPTY_COMMENT_AUTHOR_NAME_MSG,
    ];
    private const WRONG_DATA = [
        'rating' => self::WRONG_RATING_MSG,
        'comment' => self::WRONG_COMMENT_MSG,
        'bad_comment' => self::BAD_COMMENT_MSG,
        'author_name' => self::WRONG_AUTHOR_NAME_MSG,
    ];
    private const FAILURE_MSGS = [];
    private const SUCCESS_MSGS = [
        'comment' => self::SUCCESS_COMMENT_MSG,
        'rating' => self::SUCCESS_RATING_MSG,
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
        $this->messages['wrong'] = self::WRONG_DATA;
        $this->messages['success'] = self::SUCCESS_MSGS;
    }
}
