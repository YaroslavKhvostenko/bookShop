<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\User\Admin;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;

class ResultMessageModel
{
    private IDataManagement $sessionInfo;

    private const PROJECT_ERR_MSG = 'Произошла ошибка на нашей стороне.Приносим наши извинения!';

    public function __construct()
    {
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
    }

    /**
     * @param string $dataType
     * @return string
     */
    public function emptyDataMsg(string $dataType): string
    {
        switch ($dataType) {
            case 'login' :
                return 'Вы забыли указать ваш логин!';
            case 'pass' :
                return 'Вы забыли указать ваш пароль!';
            case 'pass_confirm' :
                return 'Вы забыли указать подтверждение пароля!';
            case 'name' :
                return 'Вы забыли указать ваше Имя!';
            case 'birthdate' :
                return 'Вы забыли указать вашу дату рождения!';
            case 'email' :
                return 'Вы забыли указать вашу почту!';
            case 'admin_pass' :
                return 'Вы забыли указать админовский пароль!';
            case 'phone' :
                return 'Вы забыли указать телефон!';
            case 'address' :
                return 'Вы забыли указать аддресс!';
            default : return 'Иди выровняй руки говнокодер!';
        }
    }

    /**
     * @param string $dataType
     * @return string
     */
    public function notCorrectData(string $dataType): string
    {
        switch ($dataType) {
            case 'login' :
                return 'Неправильный формат Логина!';
            case 'pass' :
                return 'Неправильный формат пароля!';
            case 'pass_confirm' :
                return 'Подверждение пароля не совпадает с паролем!';
            case 'name' :
                return 'Неправильный формат имени!';
            case 'birthdate' :
                return 'Неправильный формат даты рождения!';
            case 'email' :
                return 'Неправильный формат почты!';
            case 'phone' :
                return 'Неправильный формат телефона!';
            case 'address' :
                return 'Неправильный формат адресса!';
            case 'admin_pass' :
                return 'Вы ввели неправильный пароль админа!';
            case 'image_size' :
                return 'Слишком большая картинка! Не более 500 килобайт!';
            case 'image_type' :
                return 'Неправильный формат картинки! Только jpeg или png!';
            default : return 'Иди выровняй руки говнокодер!';
        }
    }

    /**
     * @param string $typeMsg
     * @return string
     */
    public function resultRegMsg(string $typeMsg): string
    {
        switch ($typeMsg) {
            case 'user_exist' :
                return 'Пользователь с таким логином или почтой уже зарегистрирован!';
            case 'user_reg_success' :
                return 'Вы успешно зарегистрировались!';
            case 'project_mistake' :
                return self::PROJECT_ERR_MSG;
        }
    }

    /**
     * @param $typeMsg
     * @return string
     */
    public function resultLogMsg($typeMsg): string
    {
        switch ($typeMsg) {
            case 'user_not_exist' :
                return 'Такого пользователя не существует!';
            case 'failed_pass' :
                return 'Неправильный пароль!';
            case 'failed_admin_pass' :
                return 'Неправильный пароль админа!';
            case 'not_active' :
                return 'Страница была удалена! Не желаете ли восстановить!?';
        }
    }

    /**
     * @param string $msg
     */
    public function setMsg(string $msg): void
    {
        $this->sessionInfo->setSessionMsg($msg);
    }
}