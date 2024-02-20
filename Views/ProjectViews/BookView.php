<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractBookView;
use Models\ProjectModels\Session\User\SessionModel as UserSessModel;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;

class BookView extends AbstractBookView
{
    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), UserSessModel::getInstance());
    }
}
