<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\User\Guest\Registration;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    protected const LOGIN = 'login';
    protected const PASS = 'pass';
    protected const PASS_CONFIRM = 'pass_confirm';
    protected const NAME = 'name';
    protected const BIRTHDATE = 'birthdate';
    protected const EMAIL = 'email';
    private const LOGIN_EMPTY_MSG = 'Вы забыли указать ваш логин!';
    private const PASS_EMPTY_MSG = 'Вы забыли указать ваш пароль!';
    private const PASS_CONFIRM_EMPTY_MSG = 'Вы забыли указать подтверждение пароля!';
    private const NAME_EMPTY_MSG = 'Вы забыли указать ваше имя!';
    private const BIRTHDATE_EMPTY_MSG = 'Вы забыли указать вашу дату рождения!';
    private const EMAIL_EMPTY_MSG = 'Вы забыли указать вашу почту!';
    protected const PHONE = 'phone';
    protected const ADDRESS = 'address';
    protected const IMAGE_SIZE = 'image_size';
    protected const IMAGE_TYPE = 'image_type';
    private const LOGIN_WRONG_MSG = 'Неправильный формат логина!';
    private const PASS_WRONG_MSG = 'Неправильный формат пароля!';
    private const PASS_CONFIRM_WRONG_MSG = 'Подверждение пароля не совпадает с паролем!';
    private const NAME_WRONG_MSG = 'Неправильный формат имени!';
    private const BIRTHDATE_WRONG_MSG = 'Неправильный формат даты рождения!';
    private const EMAIL_WRONG_MSG = 'Неправильный формат почты!';
    private const PHONE_WRONG_MSG = 'Неправильный формат телефона!';
    private const ADDRESS_WRONG_MSG = 'Неправильный формат адресса!';
    private const IMAGE_SIZE_WRONG_MSG = 'Слишком большая картинка!';
    private const IMAGE_TYPE_WRONG_MSG = 'Неправильный формат картинки!';
    protected const SUCCESS_REGISTRATION = self::SUCCESS_RESULT . '_registration';
    private const SUCCESS_REGISTRATION_MSG = 'Поздравляем с успешной регистрацией!';
    private const USER_EXIST_MSG = 'Пользователь с таким логином, почтой или телефоном уже существует!';
    private const EMPTY_DATA = [
        self::LOGIN => self::LOGIN_EMPTY_MSG,
        self::PASS => self::PASS_EMPTY_MSG,
        self::PASS_CONFIRM => self::PASS_CONFIRM_EMPTY_MSG,
        self::NAME => self::NAME_EMPTY_MSG,
        self::BIRTHDATE => self::BIRTHDATE_EMPTY_MSG,
        self::EMAIL => self::EMAIL_EMPTY_MSG
    ];
    private const WRONG_DATA = [
        self::LOGIN => self::LOGIN_WRONG_MSG,
        self::PASS => self::PASS_WRONG_MSG,
        self::PASS_CONFIRM => self::PASS_CONFIRM_WRONG_MSG,
        self::NAME => self::NAME_WRONG_MSG,
        self::BIRTHDATE => self::BIRTHDATE_WRONG_MSG,
        self::EMAIL => self::EMAIL_WRONG_MSG,
        self::PHONE => self::PHONE_WRONG_MSG,
        self::ADDRESS => self::ADDRESS_WRONG_MSG,
        self::IMAGE_SIZE => self::IMAGE_SIZE_WRONG_MSG,
        self::IMAGE_TYPE => self::IMAGE_TYPE_WRONG_MSG
    ];
    private const SUCCESS_MSGS = [
        self::SUCCESS_REGISTRATION => self::SUCCESS_REGISTRATION_MSG
    ];
    private const FAILURE_MSGS = [
        self::USER_ESSENCE => self::USER_EXIST_MSG
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::WRONG] = self::WRONG_DATA;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
        $this->messages[self::FAILURE_RESULT] = self::FAILURE_MSGS;
    }
}
