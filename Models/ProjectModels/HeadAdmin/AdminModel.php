<?php
declare(strict_types=1);

namespace Models\ProjectModels\HeadAdmin;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Admin\AbstractAdminModel;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class AdminModel extends AbstractAdminModel
{
    private const ADMINISTRATE = 'administrate';
    private const REMOVE = 'remove';
    private const PROVIDE = 'provide';
    private const REDIRECT = 'redirect';
    private const CONDITION_DATA = [
        self::ADMINISTRATE => self::ADMINISTRATE_CONDITION,
        self::PROVIDE => self::PROVIDE_CONDITION,
        self::REMOVE => self::REMOVE_CONDITION,
        self::REDIRECT => self::REDIRECT_CONDITION
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
        self::ADMINISTRATE => self::ADMINISTRATE_FIELDS,
        self::PROVIDE => self::PROVIDE_FIELDS,
        self::REMOVE => self::REMOVE_FIELDS,
        self::REDIRECT => self::REDIRECT_FIELDS
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
    protected function getConditionData(string $actionName): array
    {
        if (!array_key_exists($actionName, self::CONDITION_DATA)) {
            throw new \InvalidArgumentException('You forgot to add field with value in const CONDITION_DATA!');
        }

        return self::CONDITION_DATA[$actionName];
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
}
