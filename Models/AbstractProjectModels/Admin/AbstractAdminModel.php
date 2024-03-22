<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Admin;

use Models\AbstractProjectModels\AbstractDefaultModel;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractAdminModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;
    protected array $conditionData = [];
    protected array $dbFields = [];
    private const DB_FIELDS = [
        'task' => self::TASK_DB_FIELDS
    ];
    private const TASK_DB_FIELDS = [
        'task_id',
        'admin_id',
        'task_description',
        'task_status'
    ];

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
        $this->dbFields = self::DB_FIELDS;
    }

    protected function setDbMsgModel(): void
    {
        $this->db->setMessageModel($this->msgModel);
    }

    /**
     * @param string $actionName
     * @return string[]
     */
    protected function getConditionData(string $actionName): array
    {
        if (!array_key_exists($actionName, $this->conditionData)) {
            throw new \InvalidArgumentException('You forgot to add field with value in const CONDITION_DATA!');
        }

        return $this->conditionData[$actionName];
    }
}
