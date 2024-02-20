<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Message\Admin\AbstractBaseMsgModel;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Message\Admin\MsgModelsFactory;
use Models\ProjectModels\Post;

abstract class AbstractAdminController extends AbstractBaseController
{
    protected const REQUEST = 'request';
    protected const REFERER = 'referer';
    protected const ADMIN_TYPE = 'admin_type';
    protected const CONTROLLER = 'controller';
    protected const ACTION = 'action';
    protected const EMPTY = 'empty';
    protected ?IDataManagement $serverInfo = null;
    protected ?AbstractBaseMsgModel $msgModel = null;
    protected ?IDataManagement $postInfo = null;

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect('admin/' . $url);
    }

    protected function isNull($data): bool
    {
        return $data === null;
    }

    /**
     * @param string $logFileType
     * @param string $logMsg
     * @param string|null $controller
     * @param string|null $action
     * @param string|null $params
     * @throws \Exception
     */
    protected function wrongData(
        string $logFileType,
        string $logMsg,
        string $controller = null,
        string $action = null,
        string $params = null
    ): void {
        $this->getLogger()->log($logFileType, $logMsg);
        $this->msgModel->setErrorMsg();
        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
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
                case 'referer' :
                    return $this->getMsgModelByReferer();
                case 'request' :
                    return $this->getMsgModelByRequest();
                case 'default' :
                    $this->msgModel = MsgModelsFactory::getMsgModel('default');
                    return $this->msgModel;
                default :
                    throw new \Exception('Unknown MsgModel type in AbstractAdminController :' . " '$uriType'");
            }
        } else {
            return $this->msgModel;
        }
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByReferer(): AbstractBaseMsgModel
    {
        $this->msgModel = MsgModelsFactory::getMsgModel($this->getRefererAdminType(), $this->getRefererAction());

        return $this->msgModel;
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByRequest(): AbstractBaseMsgModel
    {
        $this->msgModel = MsgModelsFactory::getMsgModel($this->getRequestAdminType(), $this->getRequestAction());

        return $this->msgModel;
    }

    /**
     * @param string $option
     * @return string
     * @throws \Exception
     */
    protected function getRefererOption(string $option): string
    {
        return $this->getServerInfo()->getRefererOption($option);
    }

    /**
     * @param string $option
     * @return string
     * @throws \Exception
     */
    protected function getRequestOption(string $option): string
    {
        return $this->getServerInfo()->getRequestOption($option);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRequestAdminType(): string
    {
        return $this->getRequestOption(self::ADMIN_TYPE);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRequestController(): string
    {
        return $this->getRequestOption(self::CONTROLLER);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRequestAction(): string
    {
        return $this->getRequestOption(self::ACTION);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRefererAdminType(): string
    {
        return $this->getRefererOption(self::ADMIN_TYPE);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRefererController(): string
    {
        return $this->getRefererOption(self::CONTROLLER);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRefererAction(): string
    {
        return $this->getRefererOption(self::ACTION);
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    protected function getServerInfo(): IDataManagement
    {
        if (!$this->serverInfo) {
            $this->serverInfo = DataRegistry::getInstance()->get('server');
        }

        return $this->serverInfo;
    }

    protected function createRedirectString(
        string $controller = null,
        string $action = null,
        string $params = null
    ): string {
        $redirectString = '';
        if ($controller !== null) {
            $redirectString .= $controller;
            if ($action !== null) {
                $redirectString .= '/' . $action;
                if ($params !== null) {
                    $redirectString .= '/' . $params;
                }
            }
        }

        return $redirectString;
    }

    abstract protected function validateRequester(): bool;

    abstract protected function redirectHomeByCustomerType(): void;

    /**
     * @param \Exception $exception
     * @param string|null $controller
     * @param string|null $action
     * @param string|null $params
     * @throws \Exception
     */
    protected function catchException(
        \Exception $exception,
        string $controller = null,
        string $action = null,
        string $params = null
    ): void {
        $this->getLogger()->logException($exception);
        $this->msgModel->setErrorMsg();
        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    protected function getPostInfo(): IDataManagement
    {
        if (!$this->postInfo) {
            DataRegistry::getInstance()->register('post', new Post\Manager());
            $this->postInfo = DataRegistry::getInstance()->get('post');
        }

        return $this->postInfo;
    }

    abstract protected function redirectHome(): void;

}
