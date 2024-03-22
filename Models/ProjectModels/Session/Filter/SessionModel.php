<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session\Filter;

use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;

class SessionModel
{
    private static SessionModel $selfInstance;
    private ?IDataManagement $sessionInfo;
    private ?array $data = null;
    private const CONTROLLER_NAMES = [
        'catalog',
        'admin'
    ];
    private const ACTIONS = [
        'catalog' => self::CATALOG_ACTIONS,
        'admin' => self::ADMIN_ACTIONS,
    ];
    private const CATALOG_ACTIONS = [
        'show'
    ];
    private const ADMIN_ACTIONS = [
        'task'
    ];

    private function __construct()
    {
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->data = $this->sessionInfo->getData('filter');
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {

    }

    public static function getInstance(): self
    {
        return static::createSelf();
    }

    protected static function createSelf(): self
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }

    public function setFilter(string $controllerName, string $actionName, array $filterData): void
    {
        $this->setFilterData($controllerName, $actionName, $filterData);
    }

    private function setFilterData(string $controllerName, string $actionName, array $filterData): void
    {
        $this->validateControllerName($controllerName);
        $this->validateActionName($controllerName, $actionName);
        $_SESSION['filter'][$controllerName][$actionName] = $filterData;
    }

    public function getFilter(string $controllerName, string $actionName): array
    {
        $this->validateControllerName($controllerName);
        $this->validateActionName($controllerName, $actionName);

        return $this->data[$controllerName][$actionName];
    }

    public function issetFilter(string $controllerName, string $actionName): bool
    {
        $this->validateControllerName($controllerName);
        $this->validateActionName($controllerName, $actionName);

        return isset($this->data[$controllerName][$actionName]);
    }

    public function unsetFilter(string $controllerName, string $actionName): void
    {
        $this->validateControllerName($controllerName);
        $this->validateActionName($controllerName, $actionName);
        if (isset($this->data[$controllerName][$actionName])) {
            unset($this->data[$controllerName][$actionName]);
            unset($_SESSION['filter'][$controllerName][$actionName]);
            if (empty($this->data[$controllerName])) {
                unset($this->data[$controllerName]);
                unset($_SESSION['filter'][$controllerName]);
            }

            if (empty($this->data)) {
                $this->data = null;
                $this->sessionInfo->unsetData('filter');
            }
        }
    }

    private function validateControllerName(string $controllerName): void
    {
        if (!in_array($controllerName, self::CONTROLLER_NAMES)) {
            throw new \Exception('Unknown controller name :' . "'$controllerName' !");
        }
    }

    private function validateActionName(string $controllerName, string $actionName): void
    {
        if (!in_array($actionName, self::ACTIONS[$controllerName])) {
            throw new \Exception('Unknown action name :' . "'$actionName' !");
        }
    }
}
