<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;
//use mysql_xdevapi\Exception;

abstract class AbstractUserModel extends AbstractDefaultModel
{
    protected const CUSTOMER = 'customer';
    protected const CONTROLLER = 'controller';
    protected const ACTION = 'action';
    private const CUSTOMER_DB_TABLE = 'users';
    protected const DEFAULT_ERROR = 'default';
    protected const FILE_ERR = 'file';
    protected array $user = [];
    protected array $oldData;
    protected array $newData;
    protected IDataManagement $serverInfo;
    protected MySqlDbWorkModel $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = new MySqlDbWorkModel();
        $this->serverInfo = DataRegistry::getInstance()->get('server');
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function createUser(array $data): bool
    {
        $conditionData = $this->arrayDataCreator(['login', 'email', 'phone'], $data);
        if ($this->userExist($this->getCustomerTable(), ['id'], $conditionData)) {
            $this->msgModel->setMsg($this->msgModel->getMessage('failure', 'user'), 'user');

            return false;
        }

        if ($this->getFileInfo()->isFileSent('image')) {
            $data['image'] = $this->createUniqueFileName('image');
            if (!$this->moveUploadFile('image', $this->refCustomer(), $data['image'])) {
                $this->msgModel->errorMsgSetter(self::FILE_ERR);

                return false;
            }
        }

        $data['pass'] = password_hash($data['pass'], PASSWORD_BCRYPT);

        if (!$this->db->insertData($this->getCustomerTable(), $data)) {
            if ($this->getFileInfo()->isFileSent('image')) {
                $this->deleteFile('image', $this->refCustomer(), $data['image']);
            }
            $this->msgModel->errorMsgSetter(self::DEFAULT_ERROR);

            return false;
        } else {
            $data['id'] = $this->db->getLastInsertedId();
            $data = $this->setDbSpecialFieldsData($data);
            $this->setSessionData($data);
            $this->msgModel->setMsg(
                $this->msgModel->getMessage('success', 'success_' . $this->refAction()),
                'user'
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
     * @param string $tableName
     * @param array $requestFields
     * @param array|null $conditionData
     * @return bool
     * @throws \Exception
     */
    public function userExist(string $tableName, array $requestFields, array $conditionData = null): bool
    {
        $this->setDbMsgModel();
        $result = $this->db->selectData($tableName, $requestFields, $conditionData);
        if (isset($result[0])) {
            $this->user = $result[0];

            return true;
        }

        return false;
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function login(array $data): bool
    {
        $conditionData = $this->arrayDataCreator(['login'], $data);
        $requestFields = $this->getUserFields();
        if (!$this->userExist($this->getCustomerTable(), $requestFields, $conditionData)) {
            $this->msgModel->setMsg($this->msgModel->getMessage('failure', 'user'), 'user');

            return false;
        }

        $data['admin_pass'] = $data['admin_pass'] ?? null;
        if (!$this->passwordVerify($data['pass'], $data['admin_pass'])) {
            return false;
        }

        if ($this->getUser()['is_active'] === '1') {
            $this->setSessionData($this->getUser());
            $this->msgModel->setMsg(
                $this->msgModel->getMessage('success', 'success_' . $this->refAction()),
                'user'
            );

            return true;
        } else {
            $this->msgModel->setMsg(
                $this->msgModel->getMessage('failure', 'not_active'),
                'user'
            );

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

    protected function setSessionData(array $userData): void
    {
        $userData = array_filter(
            $userData,
            function (?string $value, string $key) {
                return !($key === 'pass' || $value === null);
            },
            ARRAY_FILTER_USE_BOTH);
        if (!empty($userData)) {
            foreach ($userData as $key => $value) {
                $this->sessionInfo->setUserData($key, $value);
            }
        }
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
            $this->msgModel->setMsg(
                $this->msgModel->getMessage('failure', 'pass'),
                'pass'
            );

            return false;
        }

        return true;
    }

    public function getUserSessionData(): ?array
    {
        return $this->sessionInfo->getUser();
    }

    public function logout(): void
    {
        $this->sessionInfo->destroy();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function profile(): bool
    {
        $conditionData = $this->userIdInArrayFromSess();
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
        if (!$this->userExist($this->getCustomerTable(), $requestFields, $conditionData)) {
            $this->msgModel->errorMsgSetter(self::DEFAULT_ERROR);

            return false;
        }
        $this->user['pass'] = 'Пароль не показывается в целях безопасности!';

        return true;
    }

    /**
     * @param array $fieldsArray
     * @param array $dataArray
     * @return array
     */
    public function arrayDataCreator(array $fieldsArray, array $dataArray): array
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
                    $result = $this->arrayDataCreator([$requestField], $this->getUserSessionData());
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
            if ($this->userExist($this->getCustomerTable(), ['id'], $data)) {
                $this->msgModel->setMsg(
                    $this->msgModel->getMessage('failure', 'exist_' . $fieldName)
                );

                return;
            }
        }

        if ($fieldName !== 'pass') {
            $this->oldData[$fieldName] = $this->getUserSessionData()[$fieldName];
            $this->newData[$fieldName] = $data[$fieldName];
        } else {
            unset($data['old_pass']);
            $this->newData[$fieldName] = password_hash($data[$fieldName], PASSWORD_BCRYPT);
        }

        if (!$this->db->updateData($this->getCustomerTable(), $this->newData, $this->userIdInArrayFromSess())) {
            $this->msgModel->errorMsgSetter();
        } else {
            $this->setSessionData($this->newData);
            $this->msgModel->setMsg($this->msgModel->getMessage('success', $fieldName), $fieldName);
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
            if ($this->userExist($this->getCustomerTable(), ['id'], $data)) {
                $this->msgModel->setMsg(
                    $this->msgModel->getMessage('failure', $fieldName)
                );

                return;
            }
        }

        $this->newData[$fieldName] = $data[$fieldName];
        if (!$this->db->updateData($this->getCustomerTable(), $this->newData, $this->userIdInArrayFromSess())) {
            $this->msgModel->errorMsgSetter();
        } else {
            $this->setSessionData($this->newData);
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
            $dbResult = $this->userExist($this->getCustomerTable(), [$fieldName], $this->userIdInArrayFromSess());
            if (!$dbResult) {
                throw new \Exception('Cannot find user pass in DB, while user is logged and session is ON!');
            }

            if (!$this->passwordVerify($data['old_pass'])) {
                $this->msgModel->setMsg($this->msgModel->getMessage('failure', $fieldName), $fieldName);

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
                    $result = $data[$fieldName] === $this->getUserSessionData()[$fieldName];
                    break;
                default :
                    throw new \Exception(
                        'Unknown field : ' . "'$fieldName'" . 'during self user data check!'
                    );
            }
        }

        if ($result) {
            $this->msgModel->setMsg(
                $this->msgModel->getMessage('failure', 'self_' . $fieldName),
                $fieldName
            );
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
        $this->deleteFile($fieldName, $this->refCustomer(), $this->oldData[$fieldName]);
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
        $conditionData = $this->userIdInArrayFromSess();
        $this->newData[$fieldName] = $this->createUniqueFileName($fieldName);
        if (!$this->moveUploadFile($fieldName, $this->refCustomer(), $this->newData[$fieldName])) {
            $this->msgModel->errorMsgSetter(self::FILE_ERR);

            return;
        }

        $this->setDbMsgModel();
        if (!$this->db->updateData($this->getCustomerTable(), $this->newData, $conditionData)) {
            $this->deleteFile($fieldName, $this->refCustomer(), $this->newData[$fieldName]);
            throw new \Exception('Problems to update user image in DB');
        } else {
            if ($this->validateRefAction('change')) {
                $this->oldData = $this->arrayDataCreator([$fieldName], $this->getUserSessionData());
            }

            $this->setSessionData($this->newData);
            $this->msgModel->setMsg($this->msgModel->getMessage('success', $fieldName), $fieldName);
        }
    }

    protected function validateRefAction(string $refAction): bool
    {
        return $this->refAction() === $refAction;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    public function delete(string $fieldName): void
    {
        if (array_key_exists($fieldName, $this->getUserSessionData())) {
            $conditionData = $this->userIdInArrayFromSess();
            $data[$fieldName] = null;
            if (!$this->db->updateData($this->getCustomerTable(), $data, $conditionData)) {
                $this->msgModel->errorMsgSetter();
            } else {
                if ($fieldName === 'image' || $fieldName === 'text_file') {
                    $this->deleteFile($fieldName, $this->refCustomer(), $this->getUserSessionData()[$fieldName]);
                }

                $this->sessionInfo->deleteUserData($fieldName);
                $this->msgModel->setMsg($this->msgModel->getMessage('success', $fieldName));
            }
        } else {
            throw new \Exception('Failure to delete data from session because it not exist there!');
        }
    }

    public function remove(string $fieldName): ?array
    {
        if (array_key_exists(strtolower($fieldName), $this->getUserSessionData())) {
            $userData[strtolower($fieldName)] = $this->getUserSessionData()[strtolower($fieldName)];
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

    protected function reqCustomer(): string
    {
        return $this->getRequestOption(self::CUSTOMER);
    }

    protected function reqController(): string
    {
        return $this->getRequestOption(self::CONTROLLER);
    }

    protected function reqAction(): string
    {
        return $this->getRequestOption(self::ACTION);
    }

    protected function refCustomer(): string
    {
        return $this->getRefererOption(self::CUSTOMER);
    }

    protected function refController(): string
    {
        return $this->getRefererOption(self::CONTROLLER);
    }

    protected function refAction(): string
    {
        return $this->getRefererOption(self::ACTION);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function userIdInArrayFromSess(): array
    {
        return $this->arrayDataCreator(['id'], $this->getUserSessionData());
    }

    protected function setDbMsgModel(): void
    {
        $this->db->setMsgModel($this->msgModel);
    }
}
