<?php
declare(strict_types=1);

namespace Models\ProjectModels\HeadAdmin;

use Models\AbstractProjectModels\Admin\AbstractTaskModel;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class TaskModel extends AbstractTaskModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    /**
     * @param array|null $condition
     * @return array|null
     * @throws \Exception
     */
    public function getAdmins(array $condition = null): ?array
    {
        $data = null;
        $approvedCondition = [
            'is_approved' => 1,
            'is_head' => 0
        ];
        $andOr = ['AND'];
        $this->setDbMsgModel();
        $dbResult = $this->db->select(['id', 'name'])->
        from(['admins'])->
        condition($approvedCondition, $andOr)->
        query()->fetchAll();

        if (!$dbResult) {
            $this->msgModel->setMessage('empty', 'empty_admins', 'empty_admins');
        } else {
            $data = $dbResult;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function newTask(array $data): bool
    {
        $result = false;
        $dbResult = $this->db->insertData('tasks', $data);
        if ($dbResult) {
            $this->msgModel->setMessage('success', 'task', 'task');
            $result = true;
        }

        return $result;
    }
}
