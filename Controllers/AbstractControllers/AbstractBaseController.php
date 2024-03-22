<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\ProjectModels\Logger;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Message\MessageModelsFactories;

abstract class AbstractBaseController
{
    protected const CONTROLLER_NAME = 'Base_Controller';
    protected AbstractSessionModel $sessionModel;
    protected ?Logger $logger = null;
    protected ?AbstractBaseMsgModel $msgModel = null;

    public function __construct(AbstractSessionModel $sessionModel) {
        $this->sessionModel = $sessionModel;
    }

    protected function getLogger(): Logger
    {
        if (!$this->logger) {
            $this->logger = Logger::getInstance();
        }

        return $this->logger;
    }

    protected function redirect(string $url = null): void
    {
        header('Location: /' . $url);
    }

    /**
     * @param string $uriType
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModel(string $uriType = 'default'): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            switch ($uriType) {
                case 'default' :
                    $this->msgModel = MessageModelsFactories::getMessageModelsFactory(self::getControllerName())
                        ::getMsgModel('default');
                    break;
                default :
                    throw new \Exception(
                        'Wrong uri type : ' . '\'' . $uriType . '\'' . '!'
                    );
            }
        }

        return $this->msgModel;
    }

    /**
     * @param \Exception $exception
     * @param string|null $controller
     * @param string|null $action
     * @param array|null $params
     * @throws \Exception
     */
    protected function catchException(
        \Exception $exception,
        string $controller = null,
        string $action = null,
        array $params = null
    ): void {
        $this->getLogger()->logException($exception);
        $this->getMsgModel()->setErrorMsg();
        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }

    protected function createRedirectString(
        string $controller = null,
        string $action = null,
        array $paramsArray = null
    ): string {
        $params = null;
        if (!is_null($paramsArray) && !empty($paramsArray)) {
            $params = implode('/', array_filter($paramsArray));
        }

        return implode('/', array_filter([$controller, $action, $params]));
    }

    protected static function getControllerName(): string
    {
        return static::CONTROLLER_NAME;
    }

    abstract protected function prepareRedirect(string $url = null): void;

    abstract protected function validateRequester(): bool;
}
