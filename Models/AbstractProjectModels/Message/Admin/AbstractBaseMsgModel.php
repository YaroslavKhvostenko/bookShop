<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Admin;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel as BaseMessageModel;

abstract class AbstractBaseMsgModel extends BaseMessageModel
{
    protected const NO_ADMINS = 'no_admins';
}
