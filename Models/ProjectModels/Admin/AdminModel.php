<?php
declare(strict_types=1);

namespace Models\ProjectModels\Admin;

use Models\AbstractProjectModels\Admin\AbstractAdminModel;
use Models\ProjectModels\Session\Admin\SessionModel;

class AdminModel extends AbstractAdminModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getTasks(): ?array
    {
        $this->setDbMsgModel();
        $conditionData['id'] = $this->sessionModel->getCustomerData()['id'];
        $dbResult = $this->db->select(['id'])->from(['admins'])->condition($conditionData)->query()->fetch();
        if (!$dbResult) {
            throw new \Exception(
                'Didn\' find admin id in DB, using admin `id` from session data!'
            );
        }
        unset($conditionData);

        $select = [
            'task_id',
            'task_description',
            'task_status'
        ];
        $conditionData['admin_id'] = $this->sessionModel->getCustomerData()['id'];
        $dbResult = $this->db->select($select)->
        from(['tasks'])->
        condition($conditionData)->
        orderBy('task_id')->
        query()->fetchAll();
        if (!$dbResult) {
            $this->msgModel->setMessage('empty', 'empty_tasks', 'empty_tasks');

            $result = null;
        } else {
            $result['tasks'] = $dbResult;
        }

        return $result;
    }
}
