<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\User;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;

abstract class AbstractMsgModel
{
    protected IDataManagement $sessionInfo;
    protected const PROJECT_ERR_MSG = 'Произошла ошибка на нашей стороне.Приносим наши извинения!';
    protected const PROJECT_MISTAKE = 'project_mistake';
    protected const EMPTY = 'empty_data';
    protected const WRONG = 'wrong_data';
    protected const REGISTRATION = 'registration';
    protected const LOGIN = 'login';
    private const EMPTY_DATA = [
    'login' => 'Вы забыли указать ваш логин!',
    'pass' => 'Вы забыли указать ваш пароль!',
    'pass_confirm' => 'Вы забыли указать подтверждение пароля!',
    'name' => 'Вы забыли указать ваше Имя!',
    'birthdate' => 'Вы забыли указать вашу дату рождения!',
    'email' => 'Вы забыли указать вашу почту!'
    ];
    private const WRONG_DATA = [
        'login' => 'Неправильный формат Логина!',
        'pass' => 'Неправильный формат пароля!',
        'pass_confirm' => 'Подверждение пароля не совпадает с паролем!',
        'name' => 'Неправильный формат имени!',
        'birthdate' => 'Неправильный формат даты рождения!',
        'email' => 'Неправильный формат почты!',
        'phone' => 'Неправильный формат телефона!',
        'address' => 'Неправильный формат адресса!',
        'image_size' => 'Слишком большая картинка! Не более 500 килобайт!',
        'image_type' => 'Неправильный формат картинки! Только jpeg или png!'
    ];
    private const REGISTRATION_RESULT = [
        'user_exist' => 'Пользователь с таким логином,почтой или телефоном уже зарегистрирован!',
        'user_reg_success' => 'Вы успешно зарегистрировались!',
        self::PROJECT_MISTAKE => self::PROJECT_ERR_MSG,
    ];
    private const LOGIN_RESULT = [
        'user_not_exist' => 'Такого пользователя не существует!',
        'failed_pass' => 'Неправильный пароль!',
        'not_active' => 'Страница была удалена! Не желаете ли восстановить!?',
    ];
    protected array $messages = [];

    public function __construct()
    {
        $this->messages = [
            self::EMPTY => self::EMPTY_DATA,
            self::WRONG => self::WRONG_DATA,
            self::REGISTRATION => self::REGISTRATION_RESULT,
            self::LOGIN => self::LOGIN_RESULT
        ];
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
    }

    public function getMessage(string $messagesType, $msgType): string
    {
        return $this->messages[$messagesType][$msgType] ?? 'Иди выровняй руки говнокодер';
    }

    /**
     * @param string $msg
     */
    public function setMsg(string $msg): void
    {
        $this->sessionInfo->setSessionMsg($msg);
    }
}
