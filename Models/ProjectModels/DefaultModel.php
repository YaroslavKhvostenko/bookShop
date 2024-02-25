<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\AbstractDefaultModel;
use Models\ProjectModels\Session\User\SessionModel;

class DefaultModel extends AbstractDefaultModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
