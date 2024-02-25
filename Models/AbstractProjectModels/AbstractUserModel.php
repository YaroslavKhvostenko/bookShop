<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;
//use mysql_xdevapi\Exception;

abstract class AbstractUserModel extends AbstractDefaultModel
{
    private const CUSTOMER_DB_TABLE = 'users';
    protected const FILE_ERR = 'file';
    protected array $user = [];
    protected array $oldData;
    protected array $newData;
    protected MySqlDbWorkModel $db;

    public function __construct(AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->db = MySqlDbWorkModel::getInstance();
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
        $dbResult = $this->db->select(['id'])->from([$this->getCustomerTable()])->condition(
            $conditionData,
            $andOr
        )->query()->fetchAll();
        if ($dbResult) {
            $this->msgModel->setMessage('failure', 'user', 'user');

            return false;
        }

        if ($this->getFileInfo()->isFileSent('image')) {
            $data['image'] = $this->createUniqueFileName('image');
            if (!$this->moveUploadFile('image', $this->sessionModel->getUserType(), $data['image'])) {
                $this->msgModel->setErrorMsg('file');

                return false;
            }
        }

        $data['pass'] = password_hash($data['pass'], PASSWORD_BCRYPT);

        if (!$this->db->insertData($this->getCustomerTable(), $data)) {
            if ($this->getFileInfo()->isFileSent('image')) {
                $this->deleteFile('image', $this->sessionModel->getUserType(), $data['image']);
            }
            $this->msgModel->setErrorMsg();

            return false;
        } else {
            $data['id'] = $this->db->getLastInsertedId();
            $data = $this->setDbSpecialFieldsData($data);
            $this->sessionModel->setCustomerData($data);
            $this->msgModel->setMessage(
                'success', 'success_' . $this->serverInfo->getRefererAction(), 'user'
            );

            return true;
        }
    }

    protected function setDbSpecialFieldsData(array $data): array
    {
        $data['is_active'] = '1';

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function login(array $data): bool
    {
        $conditionData = $this->formatArrayData(['login'], $data);
        $requestFields = $this->getUserFields();
        $dbResult = $this->db->select($requestFields)->from([$this->getCustomerTable()])
            ->condition($conditionData)->query()->fetch();
        if (!$dbResult) {
            $this->msgModel->setMessage('failure', 'user', 'user');

            return false;
        }

        $this->user = $dbResult;
        $data['admin_pass'] = $data['admin_pass'] ?? null;
        if (!$this->passwordVerify($data['pass'], $data['admin_pass'])) {
            return false;
        }

        if ($this->getUser()['is_active'] === '1') {
            $this->sessionModel->setCustomerData($this->getUser());
            $this->msgModel->setMessage(
                'success', 'success_' . $this->serverInfo->getRefererAction(), 'user'
            );

            return true;
        } else {
            $this->msgModel->setMessage('failure', 'not_active', 'user');

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
            $this->msgModel->setMessage('failure', 'pass', 'pass'
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
        $dbResult = $this->db->select($requestFields)->from([$this->getCustomerTable()])
            ->condition($this->getUserId())->query()->fetch();
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
                    $result = $this->formatArrayData([$requestField], $this->sessionModel->getCustomerData());
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
                $this->msgModel->setMessage('failure', 'exist_' . $fieldName);

                return;
            }
        }

        if ($fieldName !== 'pass') {
            $this->oldData[$fieldName] = $this->sessionModel->getCustomerData()[$fieldName];
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
            $this->sessionModel->setCustomerData($this->newData);
            $this->msgModel->setMessage('success', $fieldName, $fieldName);
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
            $dbResult = $this->db->select(['id'])->from([$this->getCustomerTable()])
                ->condition($data)->query()->fetch();
            if ($dbResult) {
                $this->msgModel->setMessage('failure', $fieldName, $fieldName);

                return;
            }
        }

        $this->newData[$fieldName] = $data[$fieldName];
        $dbResult = $this->db->update([$this->getCustomerTable()], $this->newData)
            ->condition($this->getUserId())->exec();
        if (!$dbResult) {
            $this->msgModel->setErrorMsg();
        } else {
            $this->sessionModel->setCustomerData($this->newData);
            $this->msgModel->setMessage('success', $fieldName, $fieldName);
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
            $dbResult = $this->db->select([$fieldName])->from([$this->getCustomerTable()])
                ->condition($this->getUserId())->query()->fetch();
            if (!$dbResult) {
                throw new \Exception('Cannot find user pass in DB, while user is logged and session is ON!');
            }

            $this->user = $dbResult;
            if (!$this->passwordVerify($data['old_pass'])) {
                $this->msgModel->setMessage('failure', $fieldName, $fieldName);

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
                    $result = $data[$fieldName] === $this->sessionModel->getCustomerData()[$fieldName];
                    break;
                default :
                    throw new \Exception(
                        'Unknown field : ' . "'$fieldName'" . 'during self user data check!'
                    );
            }
        }

        if ($result) {
            $this->msgModel->setMessage('failure', 'self_' . $fieldName, $fieldName);
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
        $this->deleteFile($fieldName, $this->sessionModel->getUserType(), $this->oldData[$fieldName]);
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
        if (!$this->moveUploadFile($fieldName, $this->sessionModel->getUserType(), $this->newData[$fieldName])) {
            $this->msgModel->setErrorMsg(self::FILE_ERR);

            return;
        }

        $this->setDbMsgModel();
        $dbResult = $this->db->update([$this->getCustomerTable()], $this->newData)
            ->condition($this->getUserId())->exec();
        if (!$dbResult) {
            $this->deleteFile($fieldName, $this->sessionModel->getUserType(), $this->newData[$fieldName]);
            throw new \Exception('Problems to update user image in DB');
        } else {
            if ($this->validateRefererAction('change')) {
                $this->oldData = $this->formatArrayData([$fieldName], $this->sessionModel->getCustomerData());
            }

            $this->sessionModel->setCustomerData($this->newData);
            $this->msgModel->setMessage('success', $fieldName, $fieldName);
        }
    }

    protected function validateRefererAction(string $getRefererAction): bool
    {
        return $this->serverInfo->getRefererAction() === $getRefererAction;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    public function delete(string $fieldName): void
    {
        if (array_key_exists($fieldName, $this->sessionModel->getCustomerData())) {
            $data[$fieldName] = null;
            $dbResult = $this->db->update([$this->getCustomerTable()], $data)
                ->condition($this->getUserId())->exec();
            if (!$dbResult) {
                $this->msgModel->setErrorMsg();
            } else {
                if ($fieldName === 'image' || $fieldName === 'text_file') {
                    $this->deleteFile(
                        $fieldName,
                        $this->sessionModel->getUserType(),
                        $this->sessionModel->getCustomerData()[$fieldName]
                    );
                }

                $this->sessionModel->deleteCustomerData($fieldName);
                $this->msgModel->setMessage('success', $fieldName, $fieldName);
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
        if (array_key_exists(strtolower($fieldName), $this->sessionModel->getCustomerData())) {
            $userData[strtolower($fieldName)] = $this->sessionModel->getCustomerData()[strtolower($fieldName)];
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

    /**
     * @return array
     */
    protected function getUserId(): array
    {
        return $this->formatArrayData(['id'], $this->sessionModel->getCustomerData());
    }

    protected function setDbMsgModel(): void
    {
        $this->db->setMessageModel($this->msgModel);
    }
}
