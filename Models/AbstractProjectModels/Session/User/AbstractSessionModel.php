<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Session\User;

use Models\AbstractProjectModels\Session\AbstractSessionModel as BaseSessionModel;

abstract class AbstractSessionModel extends BaseSessionModel
{
    protected const SESS_FIELD = 'user';
    protected array $dataFields = [];
    private const DATA_FIELDS = [
        'id' => 'id',
        'login' => 'login',
        'name' => 'name',
        'birthdate' => 'birthdate',
        'email' => 'email',
        'phone' => 'phone',
        'address' => 'address',
        'image' => 'image',
        'is_active' => 'is_active'
    ];

    protected function __construct()
    {
        parent::__construct();
        $this->dataFields = self::DATA_FIELDS;
    }

    public static function getInstance(): AbstractSessionModel
    {
        return static::createSelf();
    }

    public function getUserData(): ?array
    {
        return $this->data;
    }

    public function setUserData(array $userData): void
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

    public function isLogged(): bool
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

    public function deleteUserData(string $fieldName): void
    {
        $this->deleteData(self::getSessField(), $fieldName);
    }


}
