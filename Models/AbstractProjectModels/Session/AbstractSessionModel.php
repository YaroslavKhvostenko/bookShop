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
    }

    protected function __clone()
    {

    }

    protected function __wakeup()
    {

    }

    public static function getInstance(): AbstractSessionModel
    {
        return static::createSelf();
    }

    abstract protected static function createSelf(): AbstractSessionModel;



}
