<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;
//use mysql_xdevapi\Exception;

abstract class AbstractUserModel extends AbstractDefaultModel
{
    protected const USER_TYPE = 'user_type';
    protected const CUSTOMER = 'user_type';
    protected const CONTROLLER = 'controller';
    protected const ACTION = 'action';
    private const CUSTOMER_DB_TABLE = 'users';
    protected const FILE_ERR = 'file';
    protected array $user = [];
    protected array $oldData;
    protected array $newData;
    protected IDataManagement $serverInfo;
    protected MySqlDbWorkModel $db;

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
        $this->serverInfo = DataRegistry::getInstance()->get('server');
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function createUser(array $data): bool
    {
        $conditionData = $this->formatArrayData(['login', 'email', 'phone'], $data);
        $andOr = count($conditionData) === 3 ? ['OR', 'OR'] : ['OR'];
        $dbResult = $this->db->select(['id'])
                        ->from([$this->getCustomerTable()])->condition($conditionData, $andOr)->query()->fetchAll();
        if ($dbResult) {
            $this->msgModel->setMsg('failure', 'user', 'user');

            return false;
        }

        if ($this->getFileInfo()->isFileSent('image')) {
            $data['image'] = $this->createUniqueFileName('image');
            if (!$this->moveUploadFile('image', $this->getRefererUserType(), $data['image'])) {
                $this->msgModel->setErrorMsg('file');

                return false;
            }
        }

        $data['pass'] = password_hash($data['pass'], PASSWORD_BCRYPT);

        if (!$this->db->insertData($this->getCustomerTable(), $data)) {
            if ($this->getFileInfo()->isFileSent('image')) {
                $this->deleteFile('image', $this->getRefererUserType(), $data['image']);
            }
            $this->msgModel->setErrorMsg();

            return false;
        } else {
            $data['id'] = $this->db->getLastInsertedId();
            $data = $this->setDbSpecialFieldsData($data);
            $this->sessionModel->setUserData($data);
            $this->msgModel->setMsg(
                'success', 'success_' . $this->getRefererAction(), 'user'
            );

            return true;
        }
    }

    protected function setDbSpecialFieldsData(array $data): array
    {
        $data['is_active'] = '1';

        return $data;
    }

//    /**
//     * @param string $tableName
//     * @param array $requestFields
//     * @param array|null $conditionData
//     * @return bool
//     * @throws \Exception
//     */
//    public function userExist(string $tableName, array $requestFields, array $conditionData = null): bool
//    {
//        $this->setDbMsgModel();
//        $result = $this->db->selectData($tableName, $requestFields, $conditionData);
//        if (isset($result[0])) {
//            $this->user = $result[0];
//
//            return true;
//        }
//
//        return false;
//    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function login(array $data): bool
    {
        $conditionData = $this->formatArrayData(['login'], $data);
        $requestFields = $this->getUserFields();
        $dbResult = $this->db->select($requestFields)
                ->from([$this->getCustomerTable()])->condition($conditionData)->query()->fetch();
        if (!$dbResult) {
            $this->msgModel->setMsg('failure', 'user', 'user');

            return false;
        }

        $this->user = $dbResult;
        $data['admin_pass'] = $data['admin_pass'] ?? null;
        if (!$this->passwordVerify($data['pass'], $data['admin_pass'])) {
            return false;
        }

        if ($this->getUser()['is_active'] === '1') {
            $this->sessionModel->setUserData($this->getUser());
            $this->msgModel->setMsg(
                'success', 'success_' . $this->getRefererAction(), 'user'
            );

            return true;
        } else {
            $this->msgModel->setMsg('failure', 'not_active', 'user');

            return false;
        }
    }

    protected function getUserFields(): array
    {
        return [
            'id',
            'login',
            'pass',
            'name',
            'birthdate',
            'email',
            'phone',
            'address',
            'image',
            'is_active'
        ];
    }

    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * @param string $userPass
     * @param string|null $adminPass
     * @return bool
     * @throws \Exception
     */
    protected function passwordVerify(string $userPass, string $adminPass = null): bool
    {
        if (!password_verify($userPass, $this->getUser()['pass'])) {
            $this->msgModel->setMsg('failure', 'pass', 'pass'
            );

            return false;
        }

        return true;
    }

    public function logout(): void
    {
        $this->sessionModel->sessionDestroy();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function profile(): bool
    {
        $requestFields = [
            'login',
            'pass',
            'name',
            'birthdate',
            'email',
            'phone',
            'address',
            'image'
        ];
        $dbResult = $this->db->select($requestFields)
            ->from([$this->getCustomerTable()])->condition($this->getUserId())->query()->fetch();
        if (!$dbResult) {
            $this->msgModel->setErrorMsg();

            return false;
        }

        $this->user = $dbResult;
        $this->user['pass'] = 'Пароль не показывается в целях безопасности!';

        return true;
    }

    /**
     * @param array $fieldsArray
     * @param array $dataArray
     * @return array
     */
    public function formatArrayData(array $fieldsArray, array $dataArray): array
    {
        $resultArray = [];
        foreach ($fieldsArray as $field) {
            if (array_key_exists($field, $dataArray)) {
                $resultArray[$field] = $dataArray[$field];
            } elseif ($field === 'phone' && !array_key_exists($field, $dataArray)) {
                continue;
            } else {
                throw new \InvalidArgumentException(
                    'Field \'' . $field . '\' does not exist in the provided data array.'
                );
            }
        }

        return $resultArray;
    }

    /**
     * @param string $requestField
     * @return array
     * @throws \Exception
     */
    public function change(string $requestField): array
    {
        $result = [];
        if ($requestField === 'pass') {
            $result[$requestField] = 'Пароль не показывается в целях безопасности!';
        } else {
            switch ($requestField) {
                case 'login' :
                case 'name' :
                case 'birthdate' :
                case 'email' :
                case 'phone' :
                case 'address' :
                case 'image' :
                    $result = $this->formatArrayData([$requestField], $this->sessionModel->getUserData());
                    break;
                default :
                    throw new \Exception('Field :' . "'$requestField'" . 'doesn\'t exist!');

            }
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @param array $data
     * @throws \Exception
     */
    public function updateItemText(string $fieldName, array $data): void
    {
        if ($this->selfDataCheck($fieldName, $data)) {
            return;
        }

        if ($fieldName === 'login' || $fieldName === 'phone' || $fieldName === 'email') {
            $dbResult = $this->db->select(['id'])
                ->from([$this->getCustomerTable()])->condition($data)->query()->fetch();
            if ($dbResult) {
                $this->msgModel->setMsg('failure', 'exist_' . $fieldName);

                return;
            }
        }

        if ($fieldName !== 'pass') {
            $this->oldData[$fieldName] = $this->sessionModel->getUserData()[$fieldName];
            $this->newData[$fieldName] = $data[$fieldName];
        } else {
            unset($data['old_pass']);
            $this->newData[$fieldName] = password_hash($data[$fieldName], PASSWORD_BCRYPT);
        }

        $dbResult = $this->db->update([$this->getCustomerTable()], $this->newData)
            ->condition($this->getUserId())->exec();
        if (!$dbResult) {
            $this->msgModel->setErrorMsg();
        } else {
            $this->sessionModel->setUserData($this->newData);
            $this->msgModel->setMsg('success', $fieldName, $fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @param array $data
     * @throws \Exception
     */
    public function newItemText(string $fieldName, array $data): void
    {
        if ($fieldName === 'phone') {
            $dbResult = $this->db->select(['id'])
                ->from([$this->getCustomerTable()])->condition($data)->query()->fetch();
            if ($dbResult) {
                $this->msgModel->setMsg('failure', $fieldName, $fieldName);

                return;
            }
        }

        $this->newData[$fieldName] = $data[$fieldName];
        $dbResult = $this->db->update([$this->getCustomerTable()], $this->newData)
            ->condition($this->getUserId())->exec();
        if (!$dbResult) {
            $this->msgModel->setErrorMsg();
        } else {
            $this->sessionModel->setUserData($this->newData);
            $this->msgModel->setMsg($this->msgModel->getMessage('success', $fieldName), $fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    private function selfDataCheck(string $fieldName, array $data): bool
    {
        if ($fieldName === 'pass') {
            $dbResult = $this->db->select([$fieldName])
                ->from([$this->getCustomerTable()])->condition($this->getUserId())->query()->fetch();
            if (!$dbResult) {
                throw new \Exception('Cannot find user pass in DB, while user is logged and session is ON!');
            }

            $this->user = $dbResult;
            if (!$this->passwordVerify($data['old_pass'])) {
                $this->msgModel->setMsg('failure', $fieldName, $fieldName);

                return true;
            } else {
                $this->oldData[$fieldName] = $this->getUser()[$fieldName];
                $result = $this->passwordVerify($data[$fieldName]);
            }
        } else {
            switch ($fieldName) {
                case 'login' :
                case 'name' :
                case 'birthdate' :
                case 'email' :
                case 'phone' :
                case 'address' :
                    $result = $data[$fieldName] === $this->sessionModel->getUserData()[$fieldName];
                    break;
                default :
                    throw new \Exception(
                        'Unknown field : ' . "'$fieldName'" . 'during self user data check!'
                    );
            }
        }

        if ($result) {
            $this->msgModel->setMsg('failure', 'self_' . $fieldName, $fieldName);
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    public function updateItemImage(string $fieldName): void
    {
        $this->updatingFile($fieldName);
        $this->deleteFile($fieldName, $this->getRefererUserType(), $this->oldData[$fieldName]);
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    public function newItemFile(string $fieldName): void
    {
        $this->updatingFile($fieldName);
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function updatingFile(string $fieldName): void
    {
        $this->newData[$fieldName] = $this->createUniqueFileName($fieldName);
        if (!$this->moveUploadFile($fieldName, $this->getRefererUserType(), $this->newData[$fieldName])) {
            $this->msgModel->setErrorMsg(self::FILE_ERR);

            return;
        }

        $this->setDbMsgModel();
        $dbResult = $this->db->update([$this->getCustomerTable()], $this->newData)
            ->condition($this->getUserId())->exec();
        if (!$dbResult) {
            $this->deleteFile($fieldName, $this->getRefererUserType(), $this->newData[$fieldName]);
            throw new \Exception('Problems to update user image in DB');
        } else {
            if ($this->validateRefererAction('change')) {
                $this->oldData = $this->formatArrayData([$fieldName], $this->sessionModel->getUserData());
            }

            $this->sessionModel->setUserData($this->newData);
            $this->msgModel->setMsg('success', $fieldName, $fieldName);
        }
    }

    protected function validateRefererAction(string $getRefererAction): bool
    {
        return $this->getRefererAction() === $getRefererAction;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    public function delete(string $fieldName): void
    {
        if (array_key_exists($fieldName, $this->sessionModel->getUserData())) {
            $data[$fieldName] = null;
            $dbResult = $this->db->update([$this->getCustomerTable()], $data)
                ->condition($this->getUserId())->exec();
            if (!$dbResult) {
                $this->msgModel->setErrorMsg();
            } else {
                if ($fieldName === 'image' || $fieldName === 'text_file') {
                    $this->deleteFile(
                        $fieldName, $this->getRefererUserType(), $this->sessionModel->getUserData()[$fieldName]
                    );
                }

                $this->sessionModel->deleteUserData($fieldName);
                $this->msgModel->setMsg('success', $fieldName, $fieldName);
            }
        } else {
            throw new \Exception('Failure to delete data from session because it not exist there!');
        }
    }

    /**
     * @param string $fieldName
     * @return array|null
     * @throws \Exception
     */
    public function remove(string $fieldName): ?array
    {
        if (array_key_exists(strtolower($fieldName), $this->sessionModel->getUserData())) {
            $userData[strtolower($fieldName)] = $this->sessionModel->getUserData()[strtolower($fieldName)];
        } else {
            throw new \Exception(
                'Failure to get to remove_profile_item.phtml page,
                     because incoming field from RequestUriString, doesn\'t exist in user session data!
                    ');
        }

        return $userData;
    }

    protected function getCustomerTable(): string
    {
        return self::CUSTOMER_DB_TABLE;
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

    /**
     * @return array
     */
    protected function getUserId(): array
    {
        return $this->formatArrayData(['id'], $this->sessionModel->getUserData());
    }

    protected function setDbMsgModel(): void
    {
        $this->db->setMsgModel($this->msgModel);
    }
}
