<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Interfaces\Book\BookDataValidatorInterface;
use Models\AbstractProjectModels\AbstractBookModel;
use Models\AbstractProjectModels\Message\Book\AbstractBaseMsgModel;
use Views\AbstractViews\AbstractBookView;
use Models\ProjectModels\Message\Book\MsgModelsFactory;
use Models\ProjectModels\Validation\Data\Book\FactoryValidator;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Post;

abstract class AbstractBookController extends AbstractBaseController
{
    protected const REQUEST = 'request';
    protected const REFERER = 'referer';
    protected const USER_TYPE = 'user_type';
    protected const CONTROLLER = 'controller';
    protected const ACTION = 'action';
    protected const CATALOG = 'catalog';
    protected ?string $param = null;
    protected AbstractBookModel $bookModel;
    protected AbstractBookView $bookView;
    protected ?IDataManagement $postInfo = null;
    protected ?AbstractBaseMsgModel $msgModel = null;
    protected ?IDataManagement $serverInfo = null;
    protected ?BookDataValidatorInterface $dataValidator = null;


    public function __construct(AbstractBookModel $bookModel, AbstractBookView $bookView)
    {
        $this->bookModel = $bookModel;
        $this->bookView = $bookView;
    }

    /**
     * @param string $uriType
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModel(string $uriType): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            switch ($uriType) {
                case 'referer' :
                    return $this->getMsgModelByReferer();
                case 'request' :
                    return $this->getMsgModelByRequest();
                default :
                    throw new \Exception('Unknown MsgModel type in AbstractBookController :' . " '$uriType'");
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
        $this->msgModel = MsgModelsFactory::getMsgModel($this->getRefererUserType(), $this->getRefererAction());

        return $this->msgModel;
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByRequest(): AbstractBaseMsgModel
    {
        $this->msgModel = MsgModelsFactory::getMsgModel($this->getRequestUserType(), $this->getRequestAction());

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
    protected function getRequestUserType(): string
    {
        return $this->getRequestOption(self::USER_TYPE);
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
    protected function getRefererUserType(): string
    {
        return $this->getRefererOption(self::USER_TYPE);
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
     * @return BookDataValidatorInterface
     * @throws \Exception
     */
    protected function getDataValidator(string $uriType): BookDataValidatorInterface
    {
        if (!$this->dataValidator) {
            switch (strtolower($uriType)) {
                case 'request' :
                    $this->dataValidator = FactoryValidator::getValidator(
                        $this->getRequestUserType(), $this->getRequestAction()
                    );
                    break;
                case 'referer' :
                    $this->dataValidator = FactoryValidator::getValidator(
                        $this->getRefererUserType(), $this->getRefererAction()
                    );
                    break;
                default :
                    throw new \Exception('Wrong URI type declaration for creation of DataValidator');
            }
        }

        return $this->dataValidator;
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
     * @param array $result
     * @param string $messagesType
     * @param $actionType
     * @throws \Exception
     */
    protected function checkResult(
        array $result,
        string $messagesType,
        $checkType,
        string $controller,
        string $action,
        string $param = null
    ): void {
        foreach ($result as $field => $value) {
            if ($value === $checkType) {
                $this->msgModel->setMsg($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($controller, $action, $param));
    }

    abstract protected function addAction(array $params = null): void;
    abstract protected function validateRequest(): bool;
    abstract protected function redirectHome(): void;
    abstract protected function redirectHomeByCustomerType(): void;
}
