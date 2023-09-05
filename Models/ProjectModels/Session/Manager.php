<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session;

use Interfaces\IDataManagement;

/**
 * @package Models\ProjectModels\Session
 */
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

    /**
     *Initialize session data in property
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->data = $_SESSION;
        $this->messages = $_SESSION[self::MESSAGE_KEY] ?? [];
    }

    /**
     * Destroy session data
     *
     * @return void
     */
    public function destroy(): void
    {
        session_destroy();
    }

    /**
     * Get user data from session
     *
     * @return array|null
     */
    public function getUser()
    {
        return $this->data[self::USER_KEY] ?? null;
    }

    /**
     * Set session data about user
     *
     * @param string $key
     * @param string|array $data
     * @return void
     */
    public function setUserData(string $key, $data): void
    {
        $this->data[self::USER_KEY][$key] = $data;
        $_SESSION[self::USER_KEY] = $this->data[self::USER_KEY];
    }

    public function getAllMessages(): array
    {
        unset($_SESSION[self::MESSAGE_KEY]);
        return $this->messages;
    }

    public function setSessionMsg(string $msg): void
    {
        $_SESSION[self::MESSAGE_KEY][] = $msg;
    }

    public function isLogged(): bool
    {
        return isset($this->data[self::USER_KEY]);
    }

}
