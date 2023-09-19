<?php
declare(strict_types=1);

namespace Models\ProjectModels\Admin;

use Models\AbstractProjectModels\AbstractUserModel;
use Models\ProjectModels\Message\User\Admin\ResultMessageModel;
use Models\ProjectModels\DataRegistry;

class UserModel extends AbstractUserModel
{
    private string $adminPass;

    public function __construct()
    {
        parent::__construct(new ResultMessageModel());
        $this->adminPass = DataRegistry::getInstance()->get('config')->getAdminPass();
    }

    protected function setDbSpecialFieldsData(array $data): array
    {
        $data = parent::setDbSpecialFieldsData($data);
        $specialData = [
            'is_admin' => '1',
            'is_approved' => '0',
            'is_head' => '0',
        ];

        return array_merge($data, $specialData);
    }

    protected function getUserFields(): array
    {
        $defaultFields = parent::getUserFields();
        $adminFields = [
            'is_admin',
            'is_approved',
            'is_head'
        ];

        return array_merge($defaultFields, $adminFields);
    }

    /**
     * @param string $userPass
     * @param string|null $adminPass
     * @return bool
     * @throws \Exception
     */
    protected function passwordVerify(string $userPass, string $adminPass = null): bool
    {
        if (!parent::passwordVerify($userPass)) {
            return false;
        }

        if (!password_verify($adminPass, $this->adminPass)) {
            $this->msgModel->setMsg($this->msgModel->getMessage('login', 'admin_pass'));

            return false;
        }

        return true;
    }

    public function logout(): void
    {
        $this->logger->log(
            'activity',
            'Админ : ' .
            $this->sessionInfo->getUser()['login'] .
            " ({$this->sessionInfo->getUser()['name']}) " .
            'вышел.'
        );
        parent::logout();
    }

    protected function setSessionData(array $userData): void
    {
        $this->logger->log(
            'activity',
            'Админ : ' .
            $userData['login'] .
            " ({$userData['name']}) " .
            'вошел.'
        );
        parent::setSessionData($userData);
    }
}
