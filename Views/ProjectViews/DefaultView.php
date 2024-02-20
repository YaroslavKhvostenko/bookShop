<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractDefaultView;
use Models\ProjectModels\Session\Message\SessionModel as MsgSessModel;
use Models\ProjectModels\Session\User\SessionModel as UserSessModel;

class DefaultView extends AbstractDefaultView
{
    public function __construct()
    {
        parent::__construct(MsgSessModel::getInstance(), UserSessModel::getInstance());
    }

    protected function getContentPath(): string
    {
        return $this->getPath();
    }
}
