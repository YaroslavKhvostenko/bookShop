<?php
declare(strict_types=1);

namespace Models\ProjectModels\HeadAdmin;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Admin\AbstractAdminModel;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class AdminModel extends AbstractAdminModel
{
    private const CONDITION_DATA = [
        'administrate' => self::ADMINISTRATE_CONDITION,
        'provide' => self::PROVIDE_CONDITION,
        'remove' => self::REMOVE_CONDITION,
        'redirect' => self::REDIRECT_CONDITION
    ];
    private const ADMINISTRATE_CONDITION = [
        'is_active' => '1',
        'is_head' => '0'
    ];
    private const PROVIDE_CONDITION = [
        'is_active' => '1',
        'is_approved' => '0',
        'is_head' => '0'
    ];
    private const REMOVE_CONDITION = [
        'is_active' => '1',
        'is_approved' => '1',
        'is_head' => '0'
    ];
    private const DB_FIELDS = [
        'administrate' => self::ADMINISTRATE_FIELDS,
        'provide' => self::PROVIDE_FIELDS,
        'remove' => self::REMOVE_FIELDS,
        'redirect' => self::REDIRECT_FIELDS
    ];
    private const ADMINISTRATE_FIELDS = [
        'name',
        'birthdate',
        'email',
        'phone',
        'address',
        'image',
        'is_approved'
    ];
    private const PROVIDE_FIELDS = [
        'id',
        'login',
        'name',
        'birthdate',
        'email',
        'phone',
        'address',
        'image',
    ];
    private const REMOVE_FIELDS = self::PROVIDE_FIELDS;
    private const REDIRECT_FIELDS = self::PROVIDE_FIELDS;
    private const REDIRECT_CONDITION = self::REMOVE_CONDITION;

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
        $this->conditionData = self::CONDITION_DATA;
        $this->dbFields = array_merge($this->dbFields, self::DB_FIELDS);
    }

    /**
     * @param string $actionName
     * @return array|null
     * @throws \Exception
     */
    public function getAdmins(string $actionName): ?array
    {
        if ($actionName === 'administrate') {
            $andOr = ['AND'];
        } else {
            $andOr = ['AND', 'AND'];
        }

        $this->setDbMsgModel();
        $dbResult = $this->db->select($this->getDbFields($actionName))->from(['admins'])
            ->condition(
                $this->getConditionData($actionName),
                null,
                $andOr
            )->query()->fetchAll();
        if (!$dbResult) {
            $this->msgModel->setMessage('empty', 'no_admins', 'empty');

            return null;
        }

        return $dbResult;
    }

    /**
     * @param string $actionName
     * @return string[]
     */
    protected function getDbFields(string $actionName): array
    {
        if (!array_key_exists($actionName, self::DB_FIELDS)) {
            throw new \InvalidArgumentException('You forgot to add field with value in const DB_FIELDS!');
        }

        return self::DB_FIELDS[$actionName];
    }

    /**
     * @param array $data
     * @param string $fieldName
     * @throws \Exception
     */
    public function changeAccess(array $data, string $fieldName): void
    {
        $count = count($data);
        if ($fieldName === 'is_head' && $count > 1) {
            $this->msgModel->setMessage('failure', 'too_much_admins', 'too_much_admins');

            return;
        }
        
        $conditionData = $this->formatArrayData(['id'], $data);
        $this->setDbMsgModel();
        $dbResult = $this->db->select(['id'])->from(['admins'])->condition($conditionData)->query()->fetchAll();
        if (!$dbResult) {
            throw new \Exception(
                'Wrong Admin id:' . $data['id'] . ', during changeAccess->selectAdmins methods!'
            );
        }

        $updateData = $this->formatArrayData([$fieldName], $data);
        $dbResult = $this->db->update(['admins'], $updateData)->condition($conditionData)->exec();
        if ($dbResult) {
            $this->msgModel->setMessage(
                'success',
                $this->serverInfo->getRefererAction(),
                'success'
            );
            if ($fieldName === 'is_head') {
                $conditionData = $this->formatArrayData(['id'], $this->sessionModel->getCustomerData());
                $dbResult = $this->db->update(['admins'], ['is_head' => 0])->condition($conditionData)->exec();
                if ($dbResult) {
                    $this->sessionModel->setCustomerData(['is_head' => '0']);
                } else {
                    $this->getLogger()->log(
                        'default',
                        'Problems to update db data of previous HeadAdmin '.
                        $this->sessionModel->getCustomerData()['name'] . ':(' .
                        $this->sessionModel->getCustomerData()['login'] . ')!'
                    );
                }
            }
        } else {
            throw new \Exception(
                'Something went wrong during updating Admin DB data,
                 using : ' . $this->serverInfo->getRequestAction() . 'Action->changeAccess methods!'
            );
        }
    }

    /**
     * @param array $fieldNames
     * @param array $data
     * @return array
     */
    function formatArrayData(array $fieldNames, array $data): array
    {
        $result = [];

        foreach ($fieldNames as $fieldName) {
            foreach ($data as $field => $value) {
                if (is_array($value) && array_key_exists($fieldName, $value)) {
                    if (!isset($result[$fieldName])) {
                        $result[$fieldName] = [];
                    }
                    if (!in_array($value[$fieldName], $result[$fieldName])) {
                        $result[$fieldName][] = $value[$fieldName];
                    }
                } elseif ($field === $fieldName) {
                    $result[$fieldName] = $value;
                }
            }
        }

        if (empty($result)) {
            throw new InvalidArgumentException('Cannot find field in provided array data!');
        } elseif (count($result) === 1) {
            foreach ($result as $field => $value) {
                if (is_array($value) && count($value) === 1) {
                    $result[$field] = $value[0];
                }
            }
        }

        return $result;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getTasks(): ?array
    {
        $this->setDbMsgModel();
        $select = [
            'tasks`.`task_id' => 'task_id',
            'tasks`.`admin_id' => 'admin_id',
            'admins`.`name' => 'admin_name',
            'admins`.`login' => 'admin_login',
            'tasks`.`task_description' => 'task_description',
            'tasks`.`task_status' => 'task_status'
        ];
        $requestTable = ['tasks'];
        $joinTables = ['admins'];
        $joinConditions = [
            'tasks`.`admin_id' => 'admins`.`id'
        ];
        $joinTypes = ['JOIN'];
        $tasksResult = $this->db->select($select)->from(
            $requestTable,
            $joinTables,
            $joinConditions,
            $joinTypes
        )->orderBy('task_id')->
        query()->
        fetchAll();
        if (!$tasksResult) {
            $this->msgModel->setMessage('empty', 'empty_tasks', 'empty_tasks');
            $result = null;
        } else {
            $result['tasks'] = $tasksResult;
            $requestFields = [
                'id',
                'name'
            ];
            $requestTable = ['admins'];
            $conditionData = [
                'is_approved' => 1,
                'is_head' => 0
            ];
            $andOR = ['AND'];
            $adminsResult = $this->db->select($requestFields)->from($requestTable)->condition(
                $conditionData,
                null,
                $andOR
            )->query()->fetchAll();
            if (!$adminsResult) {
                $this->msgModel->setMessage('empty', 'empty_admins', 'empty_admins');
                $result = null;
            } else {
                $result['admins'] = $adminsResult;
            }
        }

        return $result;
    }
}
