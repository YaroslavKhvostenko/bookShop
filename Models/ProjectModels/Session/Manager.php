<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private const USER_KEY = 'user';
    private const MESSAGE_KEY = 'resultMsg';
    private array $messages = [];
    private ?array $data = null;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->data = $_SESSION;
        $this->messages = $_SESSION[self::MESSAGE_KEY] ?? [];
    }

    public function destroy(): void
    {
        session_destroy();
    }

    public function getUser()
    {
        return $this->data[self::USER_KEY] ?? null;
    }

    public function setUserData(string $key, string $data): void
    {
        $this->data[self::USER_KEY][$key] = $data;
        $_SESSION[self::USER_KEY] = $this->data[self::USER_KEY];
    }

    public function getAllMessages(): array
    {
        $this->unsetAllMessages();

        return $this->messages;
    }

    public function unsetAllMessages(): void
    {
        unset($_SESSION[self::MESSAGE_KEY]);
    }

    public function setSessionMsg(string $msg, string $fieldName = null): void
    {
        if ($fieldName !== null) {
            $_SESSION[self::MESSAGE_KEY][$fieldName] = $msg;
        } else {
            $_SESSION[self::MESSAGE_KEY][] = $msg;
        }
    }

    public function isLogged(): bool
    {
        return isset($this->data[self::USER_KEY]);
    }

    public function deleteUserData(string $field): void
    {
        unset($_SESSION[self::USER_KEY][$field]);
    }
}
