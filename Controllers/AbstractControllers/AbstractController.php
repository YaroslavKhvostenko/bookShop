<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Interfaces\IDataManagement;
use Interfaces\Validator\ValidatorInterface;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Post;
use Models\ProjectModels\Message\MessageModelsFactories;
use Models\ProjectModels\Validation\Data\FactoriesValidator;

abstract class AbstractController extends AbstractBaseController
{
    protected ?IDataManagement $postInfo = null;
    protected ?IDataManagement $serverInfo = null;
    protected ?ValidatorInterface $dataValidator = null;

    /**
     * @param string $logFileType
     * @param string $logMsg
     * @param string|null $controller
     * @param string|null $action
     * @param string|null $params
     * @throws \Exception
     */
    protected function processWrongRequest(
        string $logFileType,
        string $logMsg,
        string $controller = null,
        string $action = null,
        string $params = null
    ): void {
        $this->getLogger()->log($logFileType, $logMsg);
        $this->getMsgModel()->setErrorMsg();
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
                default :
                    parent::getMsgModel();
            }
        }

        return $this->msgModel;
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByReferer(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MessageModelsFactories::getMessageModelsFactory(self::getControllerName())
                ::getMsgModel($this->sessionModel->getUserType(), $this->getServerInfo()->getRefererAction());
        }

        return $this->msgModel;
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByRequest(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MessageModelsFactories::getMessageModelsFactory(self::getControllerName())
                ::getMsgModel($this->sessionModel->getUserType(), $this->getServerInfo()->getRequestAction());
        }

        return $this->msgModel;
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

    /**
     * @return ValidatorInterface|null
     * @throws \Exception
     */
    protected function getDataValidator(string $uriType): ValidatorInterface
    {
        if (!$this->dataValidator) {
            switch (strtolower($uriType)) {
                case 'request' :
                    return $this->getValidatorByRequest();
                case 'referer' :
                    return $this->getValidatorByReferer();
                default :
                    throw new \Exception('Wrong URI type declaration for creation of DataValidator');
            }
        }

        return $this->dataValidator;
    }

    /**
     * @return ValidatorInterface
     * @throws \Exception
     */
    protected function getValidatorByReferer(): ValidatorInterface
    {
        if (!$this->dataValidator) {
            $this->dataValidator = FactoriesValidator::getFactoryValidator(self::getControllerName())
                ::getValidator(
                    $this->sessionModel->getUserType(), $this->getServerInfo()->getRefererAction()
                );
        }

        return $this->dataValidator;
    }

    /**
     * @return ValidatorInterface
     * @throws \Exception
     */
    protected function getValidatorByRequest(): ValidatorInterface
    {
        if (!$this->dataValidator) {
            $this->dataValidator = FactoriesValidator::getFactoryValidator(self::getControllerName())
                ::getValidator(
                    $this->sessionModel->getUserType(), $this->getServerInfo()->getRequestAction()
                );
        }

        return $this->dataValidator;
    }
}
