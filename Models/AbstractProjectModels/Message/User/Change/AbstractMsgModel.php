<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\User\Change;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    protected const LOGIN = 'login';
    protected const OLD_PASS = 'old_pass';
    protected const PASS = 'pass';
    protected const PASS_CONFIRM = 'pass_confirm';
    protected const NAME = 'name';
    protected const BIRTHDATE = 'birthdate';
    protected const EMAIL = 'email';
    protected const PHONE = 'phone';
    protected const ADDRESS = 'address';
    protected const IMAGE = 'image';
    private const LOGIN_EMPTY_MSG = 'Вы забыли указать ваш новый логин!';
    private const OLD_PASS_EMPTY_MSG = 'Вы забыли указать ваш старый пароль';
    private const PASS_EMPTY_MSG = 'Вы забыли указать ваш новый пароль!';
    private const PASS_CONFIRM_EMPTY_MSG = 'Вы забыли подтвердить ваш новый пароль!';
    private const NAME_EMPTY_MSG = 'Вы забыли указать ваше новое имя!';
    private const BIRTHDATE_EMPTY_MSG = 'Вы забыли указать вашу новую дату рождения!';
    private const EMAIL_EMPTY_MSG = 'Вы забыли указать вашу новую почту!';
    private const PHONE_EMPTY_MSG = 'Вы забыли указать ваш новый номер телефона!';
    private const ADDRESS_EMPTY_MSG = 'Вы забыли указать ваш новый аддресс!';
    private const IMAGE_EMPTY_MSG = 'Вы забыли выбрать ваш новый Аватар!';
    protected const IMAGE_SIZE = 'image_size';
    protected const IMAGE_TYPE = 'image_type';
    private const LOGIN_WRONG_MSG = 'Неправильный формат нового логина!';
    private const OLD_PASS_WRONG_MSG = 'Неправильный формат старого пароля!';
    private const PASS_WRONG_MSG = 'Неправильный формат нового пароля!';
    private const PASS_CONFIRM_WRONG_MSG = 'Подверждение нового пароля не совпадает с новым паролем!';
    private const NAME_WRONG_MSG = 'Неправильный формат нового имени!';
    private const BIRTHDATE_WRONG_MSG = 'Неправильный формат новой даты рождения!';
    private const EMAIL_WRONG_MSG = 'Неправильный формат новой почты ( =Ъ )!';
    private const PHONE_WRONG_MSG = 'Неправильный формат нового телефона!';
    private const ADDRESS_WRONG_MSG = 'Неправильный формат нового адресса!';
    private const IMAGE_SIZE_WRONG_MSG = 'Слишком большая картинка!';
    private const IMAGE_TYPE_WRONG_MSG = 'Неправильный формат картинки!';
    private const LOGIN_SUCCESS_MSG = 'Ваш логин был успешно изменен!';
    private const PASS_SUCCESS_MSG = 'Ваш пароль был успешно изменен!';
    private const NAME_SUCCESS_MSG = 'Ваше имя было успешно изменено!';
    private const BIRTHDATE_SUCCESS_MSG = 'Ваша дата рождения была успешно изменена!';
    private const EMAIL_SUCCESS_MSG = 'Ваша почта была успешно изменена!';
    private const PHONE_SUCCESS_MSG = 'Ваш телефон был успешно изменен!';
    private const ADDRESS_SUCCESS_MSG = 'Ваш аддресс был успешно изменен!';
    private const IMAGE_SUCCESS_MSG = 'Ваш аватар был успешно изменен!';
    protected const WRONG_PASS_FAILURE_MSG = 'Вы не подтвердили знание старого пароля! Вспоминайте иначе пароль не поменять!';
    private const SELF_LOGIN_FAILURE_MSG = 'Менять свой логин на свой же логин... Дурашлеп)))';
    private const SELF_PASS_FAILURE_MSG = 'Менять свой пароль на свой же пароль... Дурашлеп)))';
    private const SELF_NAME_FAILURE_MSG = 'Менять своё имя на своё же имя... Дурашлеп)))';
    private const SELF_BIRTHDATE_FAILURE_MSG = 'Менять свою дату рождения на свою же дату рождения... Дурашлеп)))';
    private const SELF_EMAIL_FAILURE_MSG = 'Менять свою почту на свою же почту... Дурашлеп)))';
    private const SELF_PHONE_FAILURE_MSG = 'Менять свой номер телефона на свой же номер телефона... Дурашлеп)))';
    private const SELF_ADDRESS_FAILURE_MSG = 'Менять свой аддресс на свой же аддресс... Дурашлеп)))';
    private const EXIST_LOGIN_FAILURE_MSG = 'Невозможно сохранить ваш новый логин! Попробуйте другой!';
    private const EXIST_EMAIL_FAILURE_MSG = 'Невозможно сохранить вашу новую почту! Попробуйте другую!';
    private const EXIST_PHONE_FAILURE_MSG = 'Невозможно сохранить ваш новый номер телефона! Попробуйте другой!';
    protected const SELF_LOGIN = 'self_' . self::LOGIN;
    protected const SELF_PASS = 'self_' . self::PASS;
    protected const SELF_NAME = 'self_' . self::NAME;
    protected const SELF_BIRTHDATE = 'self_' . self::BIRTHDATE;
    protected const SELF_EMAIL = 'self_' . self::EMAIL;
    protected const SELF_PHONE = 'self_' . self::PHONE;
    protected const SELF_ADDRESS = 'self_' . self::ADDRESS;
    protected const EXIST_LOGIN = 'exist_' . self::LOGIN;
    protected const EXIST_EMAIL = 'exist_' . self::EMAIL;
    protected const EXIST_PHONE = 'exist_' . self::PHONE;
    private const EMPTY_DATA = [
        self::LOGIN => self::LOGIN_EMPTY_MSG,
        self::OLD_PASS => self::OLD_PASS_EMPTY_MSG,
        self::PASS => self::PASS_EMPTY_MSG,
        self::PASS_CONFIRM => self::PASS_CONFIRM_EMPTY_MSG,
        self::NAME => self::NAME_EMPTY_MSG,
        self::BIRTHDATE => self::BIRTHDATE_EMPTY_MSG,
        self::EMAIL => self::EMAIL_EMPTY_MSG,
        self::PHONE => self::PHONE_EMPTY_MSG,
        self::ADDRESS => self::ADDRESS_EMPTY_MSG,
        self::IMAGE => self::IMAGE_EMPTY_MSG
    ];
    private const WRONG_DATA = [
        self::LOGIN => self::LOGIN_WRONG_MSG,
        self::OLD_PASS => self::OLD_PASS_WRONG_MSG,
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
        self::LOGIN => self::LOGIN_SUCCESS_MSG,
        self::PASS => self::PASS_SUCCESS_MSG,
        self::NAME => self::NAME_SUCCESS_MSG,
        self::BIRTHDATE => self::BIRTHDATE_SUCCESS_MSG,
        self::EMAIL => self::EMAIL_SUCCESS_MSG,
        self::PHONE => self::PHONE_SUCCESS_MSG,
        self::ADDRESS => self::ADDRESS_SUCCESS_MSG,
        self::IMAGE => self::IMAGE_SUCCESS_MSG
    ];
    private const FAILURE_MSGS = [
        self::PASS => self::WRONG_PASS_FAILURE_MSG,
        self::SELF_LOGIN => self::SELF_LOGIN_FAILURE_MSG,
        self::SELF_PASS => self::SELF_PASS_FAILURE_MSG,
        self::SELF_NAME => self::SELF_NAME_FAILURE_MSG,
        self::SELF_BIRTHDATE => self::SELF_BIRTHDATE_FAILURE_MSG,
        self::SELF_EMAIL => self::SELF_EMAIL_FAILURE_MSG,
        self::SELF_PHONE => self::SELF_PHONE_FAILURE_MSG,
        self::SELF_ADDRESS => self::SELF_ADDRESS_FAILURE_MSG,
        self::EXIST_LOGIN => self::EXIST_LOGIN_FAILURE_MSG,
        self::EXIST_EMAIL => self::EXIST_EMAIL_FAILURE_MSG,
        self::EXIST_PHONE => self::EXIST_PHONE_FAILURE_MSG
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::WRONG] = self::WRONG_DATA;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
        $this->messages[self::FAILURE_RESULT] = self::FAILURE_MSGS;
    }
}
