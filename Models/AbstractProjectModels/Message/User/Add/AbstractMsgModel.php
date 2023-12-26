<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\User\Add;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    protected const IMAGE = 'image';
    private const IMAGE_EMPTY_MSG = 'Вы забыли выбрать аватар!';
    protected const IMAGE_SIZE = 'image_size';
    protected const IMAGE_TYPE = 'image_type';
    private const IMAGE_SIZE_WRONG_MSG = 'Слишком большая картинка!';
    private const IMAGE_TYPE_WRONG_MSG = 'Неправильный формат картинки!';
    private const IMAGE_SUCCESS_MSG = 'Ваш аватар был успешно добавлен!';
    private const EMPTY_DATA = [
        self::IMAGE => self::IMAGE_EMPTY_MSG
    ];
    private const WRONG_DATA = [
        self::IMAGE_SIZE => self::IMAGE_SIZE_WRONG_MSG,
        self::IMAGE_TYPE => self::IMAGE_TYPE_WRONG_MSG
    ];
    private const SUCCESS_MSGS = [
        self::IMAGE => self::IMAGE_SUCCESS_MSG
    ];

    public function __construct()
    {
        parent::__construct();
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::WRONG] = self::WRONG_DATA;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
    }
}
