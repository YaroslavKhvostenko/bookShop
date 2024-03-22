<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;

abstract class AbstractOrderModel
{
    protected AbstractSessionModel $userSessionModel;
    protected ?AbstractBaseMsgModel $msgModel = null;

    public function __construct(AbstractSessionModel $userSessionModel)
    {
        $this->userSessionModel = $userSessionModel;
    }

    public function setMessageModel(AbstractBaseMsgModel $msgModel): void
    {
        if (!$this->msgModel) {
            $this->msgModel = $msgModel;
        }
    }
}
