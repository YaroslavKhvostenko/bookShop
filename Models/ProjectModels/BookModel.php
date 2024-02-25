<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\AbstractBookModel;
use Models\ProjectModels\Session\User\SessionModel;

class BookModel extends AbstractBookModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
