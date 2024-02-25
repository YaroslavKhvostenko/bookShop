<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Session;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;

abstract class AbstractSessionModel
{
    private const SESS_FIELD = '';
    protected IDataManagement $sessionInfo;
    protected ?array $data = null;

    protected function __construct()
    {
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->initializeData();
    }

    protected function __clone()
    {

    }

    protected function __wakeup()
    {

    }

    abstract public static function getInstance();

    abstract protected static function createSelf();

    protected static function getSessField(): string
    {
        return static::SESS_FIELD;
    }

    protected function initializeData(): void
    {
        $this->data = $this->sessionInfo->getData(self::getSessField());
    }

    protected function setData(string $data, string $dataField = null): void
    {
        $this->sessionInfo->setData(self::getSessField(), $data, $dataField);
    }

    protected function deleteData(string $sessionField, $dataField = null): void
    {
        $this->sessionInfo->unsetData($sessionField, $dataField);
    }
}
