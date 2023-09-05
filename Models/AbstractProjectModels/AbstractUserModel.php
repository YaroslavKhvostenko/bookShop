<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Models\AbstractProjectModels\Message\User\AbstractMsgModel;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractUserModel extends AbstractDefaultModel
{
    protected MySqlDbWorkModel $db;
    protected AbstractMsgModel $msgModel;
    protected array $user = [];

    public function __construct(AbstractMsgModel $msgModel)
    {
        parent::__construct();
        $this->msgModel = $msgModel;
        $this->db = new MySqlDbWorkModel();
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return bool
     */
    public function newUser(string $tableName, array $data): bool
    {
        if ($this->userExist($tableName, $data['login'], $data['email'], $data['phone'])) {
            $this->msgModel->setMsg($this->msgModel->getMessage('registration','user_exist'));
            return false;
        }

        $data['pass'] = password_hash($data['pass'], PASSWORD_BCRYPT);
        if ($this->fileInfo->isImageSent()) {
            $data['image'] = $this->moveUploadFile($tableName);
        }

        if (!$this->db->insertData($tableName, $data)) {
            $this->msgModel->setMsg($this->msgModel->getMessage('registration', 'project_mistake'));
            return false;
        } else {
            $data = $this->setDbSpecialFieldsData($data);
            $this->setSessionData($data);
            $this->msgModel->setMsg($this->msgModel->getMessage('registration', 'user_reg_success'));
            return true;
        }
    }

    protected function setDbSpecialFieldsData(array $data): array
    {
        $specialData['is_active'] = 1;
        return array_merge($data, $specialData);
    }

    /**
     * @param string $tableName
     * @param string $userLogin
     * @param string|null $userEmail
     * @return bool
     */
    public function userExist(
        string $tableName,
        string $userLogin,
        string $userEmail = null,
        string $userPhone = null
    ): bool {
        $condition = '`login` = ' . $this->db->getConnection()->quote($userLogin);

        if ($userEmail !== null) {
            $condition .= ' OR `email` = ' . $this->db->getConnection()->quote($userEmail);
        }

        if ($userPhone !== null) {
            $condition .= ' OR `phone` = ' . $this->db->getConnection()->quote($userPhone);
        }

        $fields = $this->getUserFields();
        $result = $this->db->selectData($tableName, $fields, $condition);
        if ($result && $userEmail !== null) { // for registration don't need to fill @array $this->user
            return true;
        } elseif ($result) { //for login we need to fill @array $this->user
            $this->user = $result[0];
            return true;
        }

        return false;
    }

    /**
     * Check type of user and login on site
     * @param string $tableName
     * @param array $data
     * @return bool
     */
    public function login(string $tableName, array $data): bool
    {
        if (!$this->userExist($tableName, $data['login'])) {
            $this->msgModel->setMsg($this->msgModel->getMessage('login', 'user_not_exist'));
            return false;
        }

        $data['admin_pass'] = $data['admin_pass'] ?? null;
        if (!$this->passwordVerify($data['pass'], $data['admin_pass'])) {
            return false;
        }

        if ($this->getUser()['is_active'] == 1) {
            $this->setSessionData($this->getUser());
            return true;
        } else {
            $this->msgModel->setMsg($this->msgModel->getMessage('login', 'not_active'));

            return false;
        }
    }

    protected function getUserFields(): array
    {
        return ['id', 'login', 'pass', 'name', 'birthdate',
            'email', 'phone', 'address', 'image', 'is_active'];
    }

    /**
     * returns data of existing user
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * Set session data for a user
     *
     * @param array $userData
     * @return void
     */
    protected function setSessionData(array $userData): void
    {
        foreach ($userData as $key => $value) {
            if ($key == 'pass') {
                continue;
            }
            $this->sessionInfo->setUserData($key, $value);
        }
    }

    protected function passwordVerify(string $userPass, string $adminPass = null): bool
    {
        if (!password_verify($userPass, $this->getUser()['pass'])) {
            $this->msgModel->setMsg($this->msgModel->getMessage('login', 'failed_pass'));
            return false;
        }

        return true;
    }

    public function logout(): void
    {
        $this->sessionInfo->destroy();
    }
}
