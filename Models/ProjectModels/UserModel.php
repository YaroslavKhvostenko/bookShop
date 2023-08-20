<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Interfaces\IDataManagement;
use Models\ProjectModels\Message\User\ResultMessageModel;

/**
 * @package Models\ProjectModels
 */
class UserModel extends DefaultModel
{
    private ResultMessageModel $msgModel;

    private array $user = [];

    public function __construct()
    {
        parent::__construct();
        $this->msgModel = new ResultMessageModel();
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return bool
     */
    public function newUser(string $tableName, array $data): bool
    {
        if (!$this->userExist($tableName, $data['login'], $data['email'])) {
            $data['pass'] = password_hash($data['pass'], PASSWORD_BCRYPT);
            if ($this->fileInfo->isImageSent()) {
                $data['image'] = $this->moveUploadFile($tableName);
            }

            if (!$this->db->insertData($tableName, $data)) {
                $this->msgModel->setMsg($this->msgModel->resultRegMsg('project_mistake'));
                return false;
            } else {
                $this->setSessionData($data);
                $this->msgModel->setMsg($this->msgModel->resultRegMsg('user_reg_success'));
                return true;
            }
        } else {
            $this->msgModel->setMsg($this->msgModel->resultRegMsg('user_exist'));
            return false;
        }
    }

    /**
     * @param string $tableName
     * @param string $userLogin
     * @param string|null $userEmail
     * @return bool
     */
    public function userExist(string $tableName, string $userLogin, string $userEmail = null): bool
    {
        $condition = '`login` = ' . $this->db->getConnection()->quote($userLogin);
        if ($userEmail !== null) {
            $condition .= ' OR `email` = ' . $this->db->getConnection()->quote($userEmail);
        }
        $field = ['id', 'login', 'pass', 'name',
            'birthdate', 'email', 'phone', 'address', 'image', 'is_active'];
        $result = $this->db->selectData($tableName, $field, $condition);
        if ($result && $userEmail !== null) { // for registration no needed exist_user_data
            return true;
        } elseif ($result) { //for login we need exist_user_data
            $this->user = $result[0];
            return true;
        }

        return false;
    }

    /**
     * Set session data for a user
     *
     * @param array $userData
     * @return void
     */
    private function setSessionData(array $userData): void
    {
        foreach ($userData as $key => $value) {
            if ($key == 'pass') {
                continue;
            }
            $this->sessionInfo->setUserData($key, $value);
        }
    }

    public function logout(): void
    {
        $this->sessionInfo->destroy();
    }

    /**
     * Check type of user and login on site
     * @param string $tableName
     * @param array $data
     * @return bool
     */
    public function login(string $tableName, array $data): bool
    {
        if ($this->userExist($tableName, $data['login'])) {
            if (password_verify($data['pass'], $this->getUser()['pass'])) {
                if ((int)$this->getUser()['is_active'] === 1) {
                    $this->setSessionData($this->getUser());
                    return true;
                } else {
                    $this->msgModel->setMsg($this->msgModel->resultLogMsg('not_active'));
                    return false;
                }
            } else {
                $this->msgModel->setMsg($this->msgModel->resultLogMsg('failed_pass'));
                return false;
            }
        } else {
            $this->msgModel->setMsg($this->msgModel->resultLogMsg('user_not_exist'));
            return false;
        }
    }

    /**
     * returns data of existing user
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }
}
