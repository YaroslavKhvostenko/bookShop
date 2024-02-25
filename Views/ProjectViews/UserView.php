<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractUserView;
use Models\ProjectModels\Session\User\SessionModel;

class UserView extends AbstractUserView
{
    private const AVATAR_ADDRESS = 'users/';
    private const NOT_REQUIRED_PROFILE_ITEMS = [
        'phone' => 'phone',
        'address' => 'address'
    ];
    private const REMOVE_MSGS = [
        'phone' => 'Вы уверены что хотите удалить ваш телефон !? : ',
        'address' => 'Вы уверены что хотите удалить ваш аддресс !? : '
    ];
    private const ADD_LABELS = [
        'phone' => 'Введите новый номер телефона : ',
        'address' => 'Введите новый аддресс : '
    ];

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
        $this->formOptions['add']['phone'] = $this->formOptions['change']['phone'];
        $this->formOptions['add']['address'] = $this->formOptions['change']['address'];
        $this->formLabels['add'] = array_merge($this->formLabels['add'], self::ADD_LABELS);
        $this->messages['remove'] = array_merge($this->messages['remove'], self::REMOVE_MSGS);
        $this->profileItems['not_required'] = array_merge($this->profileItems['not_required'],
            self::NOT_REQUIRED_PROFILE_ITEMS);

    }
}
