<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\AbstractProjectModels\Session\Message\AbstractSessionModel as MsgSessModel;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel as CustomerSessModel;

abstract class AbstractBookView extends AbstractDefaultView
{
    public function __construct(MsgSessModel $msgSessModel, CustomerSessModel $userSessModel)
    {
        parent::__construct($msgSessModel, $userSessModel);
    }

    protected function getContentPath(): string
    {
        return $this->getPath();
    }
}

