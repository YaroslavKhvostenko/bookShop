<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractBookView;
use Models\ProjectModels\Session\User\SessionModel;

class BookView extends AbstractBookView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }
}
