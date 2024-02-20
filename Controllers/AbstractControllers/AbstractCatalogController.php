<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\AbstractCatalogModel;
use Views\AbstractViews\AbstractCatalogView;
use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;
use Models\ProjectModels\Message\Catalog\MsgModelsFactory;
use Models\ProjectModels\DataRegistry;

abstract class AbstractCatalogController extends AbstractBaseController
{
    protected AbstractCatalogModel $catalogModel;
    protected AbstractCatalogView $catalogView;
    protected ?AbstractBaseMsgModel $msgModel = null;
    protected ?IDataManagement $serverInfo = null;

    public function __construct(AbstractCatalogModel $catalogModel, AbstractCatalogView $catalogView)
    {
        $this->catalogModel = $catalogModel;
        $this->catalogView = $catalogView;
    }

    /**
     * @throws \Exception
     */
    public function showAction(): void
    {
        try {
            if ($this->validateRequest()) {
                $this->prepareRedirect();
            } else {
                $this->catalogModel->setMsgModel($this->getMsgModel('request'));
                $data = $this->catalogModel->catalog();
                $options = $this->catalogView->getOptions('Каталог', 'catalog.phtml', $data);
                $this->catalogView->render($options);
            }
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param string $uriType
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModel(string $uriType = 'default'): AbstractBaseMsgModel
    {
        switch ($uriType) {
            case 'referer' :
                return $this->getMsgModelByReferer();
            case 'request' :
                return $this->getMsgModelByRequest();
            case 'default' :
                $this->msgModel = MsgModelsFactory::getMsgModel('default');
                return $this->msgModel;
            default :
                throw new \Exception('Unknown MsgModel type in AbstractUserController :' . " '$uriType'");
        }
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
        return $this->getRequestOption('user_type');
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRequestController(): string
    {
        return $this->getRequestOption('controller');
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRequestAction(): string
    {
        return $this->getRequestOption('action');
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRefererAction(): string
    {
        return $this->getRefererOption('action');
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

    abstract protected function getMsgModelByReferer(): AbstractBaseMsgModel;

    abstract protected function getMsgModelByRequest(): AbstractBaseMsgModel;

    abstract protected function validateRequest(): bool;
}
