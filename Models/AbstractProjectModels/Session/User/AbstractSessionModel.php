<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Session\User;

use Models\AbstractProjectModels\Session\AbstractSessionModel as BaseSessionModel;

abstract class AbstractSessionModel extends BaseSessionModel
{
    protected const SESS_FIELD = 'user';
    protected string $userType = 'guest';

    protected function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(): AbstractSessionModel
    {
        return static::createSelf();
    }

    public function getCustomerData(): ?array
    {
        return $this->data;
    }

    public function setCustomerData(array $userData): void
    {
        $userData = array_filter(
            $userData,
            function (?string $value, string $key) {
                return !($key === 'pass' || $value === null);
            },
            ARRAY_FILTER_USE_BOTH);
        if (!empty($userData)) {
            foreach ($userData as $key => $value) {
                $this->data[$key] = $value;
                $this->setData($value, $key);
            }
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->data !== null;
    }

    public function isAdmin(): bool
    {
        return isset($this->data['is_admin']);
    }

    public function sessionDestroy(): void
    {
        $this->sessionInfo->destroySession();
    }

    public function deleteCustomerData(string $fieldName): void
    {
        $this->deleteData(self::getSessField(), $fieldName);
    }

    public function getUserType(): string
    {
        if ($this->isLoggedIn()) {
            $this->setUserType();
        }

        return $this->userType;
    }

    abstract protected function setUserType(): void;
}
