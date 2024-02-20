<?php
declare(strict_types=1);

namespace Models\ProjectModels\Admin;

use Models\AbstractProjectModels\AbstractUserModel;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Session\User\Admin\SessionModel;

class UserModel extends AbstractUserModel
{
    private const CUSTOMER_DB_TABLE = 'admins';
    private string $adminPass;

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
        $this->adminPass = DataRegistry::getInstance()->get('config')->getAdminPass();
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function createUser(array $data): bool
    {
        if (parent::createUser($data)) {
            $this->logAdminActivity($this->sessionModel->getUserData(), 'зарегестрировался и вошёл');

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
        if (parent::login($data)) {
            $this->logAdminActivity($this->sessionModel->getUserData(), 'вошёл');

            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $this->logAdminActivity($this->sessionModel->getUserData(), 'вышел');

        parent::logout();
    }

    private function logAdminActivity(array $data, string $activityAction): void
    {
        $this->getLogger()->log(
            'activity',
            'Админ : ' . $data['login'] . " ({$data['name']}) " . "$activityAction!"
        );
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

        if ($adminPass !== null) {
            if (!password_verify($adminPass, $this->adminPass)) {
                $this->msgModel->setMsg('failure', 'admin_pass', 'admin_pass');

                return false;
            }
        }

        return true;
    }

    protected function getCustomerTable(): string
    {
        return self::CUSTOMER_DB_TABLE;
    }
}
