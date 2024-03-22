<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Admin;

use Models\AbstractProjectModels\AbstractDefaultModel;
use Models\AbstractProjectModels\Session\Admin\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractTaskModel extends AbstractDefaultModel
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

    /**
     * @param array $data
     * @throws \Exception
     */
    public function updateTask(array $data): void
    {
        $this->setDbMsgModel();
        if ((!isset($data['condition_data']) || empty($data['condition_data'])) ||
            (!isset($data['update_data']) || empty($data['update_data']))) {
            throw new \Exception('You forgot some data in Validator!');
        }

        $conditionField = array_key_first($data['condition_data']);
        if (is_array($data['condition_data'][$conditionField])) {
            $count = count($data['condition_data'][$conditionField]);
        } else {
            $count = count($data['condition_data']);
        }

        $updateField = array_key_first($data['update_data']);
        if (is_array($data['update_data'][$updateField])) {
            $affectedRows = 1;
            try {
                $this->db->beginTransaction();
                foreach ($data['update_data'][$updateField] as $value) {
                    $updateData[$updateField] = $value;
                    $conditionData[$conditionField] = array_shift($data['condition_data'][$conditionField]);
                    $dbResult = $this->db->update(['tasks'], $updateData)->condition($conditionData)->exec();
                    if ($dbResult && $affectedRows !== $count) {
                        $affectedRows++;
                    }
                }
                $this->db->commit();
            } catch (\PDOException $exception) {
                $this->db->rollBack();
            }
        } else {
            $updateData = $data['update_data'];
            $conditionData = $data['condition_data'];
            $affectedRows = $this->db->update(['tasks'], $updateData)->condition($conditionData)->exec();
        }

        if (!$affectedRows || $affectedRows !== $count) {
            throw new \Exception(
                'Problems with updating data in Db table `tasks`, check what comes into sql string!'
            );
        }

        $this->msgModel->setMessage('success', 'task', 'success_task');
    }
}
