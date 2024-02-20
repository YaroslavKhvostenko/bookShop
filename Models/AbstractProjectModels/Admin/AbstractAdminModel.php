<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Admin;

use Models\AbstractProjectModels\AbstractDefaultModel;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;
use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;
//use Models\ProjectModels\Session\User\Admin\SessionModel;

abstract class AbstractAdminModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;
    protected IDataManagement $serverInfo;
    protected const USER_TYPE = 'user_type';
    protected const CONTROLLER = 'controller';
    protected const ACTION = 'action';

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
        $this->serverInfo = DataRegistry::getInstance()->get('server');
    }

    protected function getRefererOption(string $option): string
    {
        return $this->serverInfo->getRefererOption($option);
    }

    protected function getRequestOption(string $option): string
    {
        return $this->serverInfo->getRequestOption($option);
    }

    protected function getRequestUserType(): string
    {
        return $this->getRequestOption(self::USER_TYPE);
    }

    protected function getRequestController(): string
    {
        return $this->getRequestOption(self::CONTROLLER);
    }

    protected function getRequestAction(): string
    {
        return $this->getRequestOption(self::ACTION);
    }

    protected function getRefererUserType(): string
    {
        return $this->getRefererOption(self::USER_TYPE);
    }

    protected function getRefererController(): string
    {
        return $this->getRefererOption(self::CONTROLLER);
    }

    protected function getRefererAction(): string
    {
        return $this->getRefererOption(self::ACTION);
    }
}
