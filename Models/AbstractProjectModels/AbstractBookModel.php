<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractBookModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
    }

    protected function setDbMsgModel(): void
    {
        $this->db->setMessageModel($this->msgModel);
    }
}
