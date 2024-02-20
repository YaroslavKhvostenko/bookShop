<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;
use Models\ProjectModels\DataRegistry;

abstract class AbstractBookModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;
    protected ?IDataManagement $serverInfo = null;

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    protected function getServerInfo(): IDataManagement
    {
        if (!$this->serverInfo) {
            $this->serverInfo = DataRegistry::getInstance()->get('server');
        }

        return $this->serverInfo;
    }
}
