<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractController;
use Models\ProjectModels\BasketModel;
use Models\ProjectModels\Session\User\SessionModel;
use Views\ProjectViews\BasketView;

class BasketController extends AbstractController
{
    protected const CONTROLLER_NAME = 'Basket_Controller';
    private BasketModel $basketModel;
    private ?BasketView $basketView = null;

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
        $this->basketModel = new BasketModel();
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function addAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have not to be NULL!',
                    $this->serverInfo->getRefererController(),
                    $this->serverInfo->getRefererAction()
                );

                return;
            }

            $result = $this->getDataValidator('request')->validateParams($params);
            $this->basketModel->setMessageModel($this->msgModel);
            $this->basketModel->addItem($result);
            $this->prepareRedirect(
                $this->createRedirectString(
                    $this->serverInfo->getRefererController(),
                    $this->serverInfo->getRefererAction()
                )
            );
        } catch (\Exception $exception) {
            $this->catchException(
                $exception,
                $this->getServerInfo()->getRefererController(),
                $this->serverInfo->getRefererAction()
            );
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function showAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            $this->getMsgModel('request');
            if (!is_null($params)) {
                $this->processWrongRequest('default', 'Params have to be NULL!');

                return;
            }

            $this->basketModel->setMessageModel($this->msgModel);
            if ($this->basketModel->isEmptyBasket()) {
                $basketData = null;
                $this->msgModel->setMessage('empty', 'empty_basket', 'empty_basket');
            } else {
                $basketData = $this->basketModel->getBasket();
            }

            $this->getView()->render($this->basketView->getOptions('Корзина', 'basket.phtml', $basketData));
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function clearAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be NULL!',
                    'basket',
                    'show'
                );

                return;
            }

            $this->basketModel->clearBasket();
            $this->msgModel->setMessage('success', 'clear_basket', 'success_clear_basket');
            $this->prepareRedirect(
                $this->createRedirectString('basket', 'show')
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'basket', 'show');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function updateAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (!is_null($params)) {
                $this->processWrongRequest('default', 'Params have to be NULL!');

                return;
            }

            $result = $this->getDataValidator('request')->emptyCheck($this->postInfo->getData());
            if (in_array('', $result)) {
                $this->checkResult($result, 'empty', '', 'basket', 'show');

                return;
            }

            $result = $this->dataValidator->correctCheck($result);
            if (in_array('', $result)) {
                $this->checkResult($result, 'wrong', '', 'basket', 'show');

                return;
            }

            $this->basketModel->setMessageModel($this->msgModel);
            $this->basketModel->updateBasket($result);
            $this->prepareRedirect(
                $this->createRedirectString('basket', 'show')
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'basket', 'show');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function removeAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (is_null($params)) {
                $this->processWrongRequest('default', 'Params have to be not NULL!');

                return;
            }

            $productId = $this->getDataValidator('request')->validateParams($params);
            $this->basketModel->setMessageModel($this->msgModel);
            $this->basketModel->removeItem($productId);
            $this->prepareRedirect(
                $this->createRedirectString('basket', 'show')
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'basket', 'show');
        }
    }

    protected function validateRequester(): bool
    {
        return $this->sessionModel->isAdmin();
    }

    protected function redirectHome(): void
    {
        $this->redirect();
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }

    private function getView(): BasketView
    {
        if (!$this->basketView) {
            $this->basketView = new BasketView();
        }

        return $this->basketView;
    }

    /**
     * @param array $result
     * @param string $messagesType
     * @param $checkType
     * @param string|null $controller
     * @param string|null $action
     * @param array|null $params
     * @throws \Exception
     */
    protected function checkResult(
        array $result,
        string $messagesType,
        $checkType,
        string $controller = null,
        string $action = null,
        array $params = null
    ): void {
        foreach ($result as $field => $value) {
            if ($value === $checkType) {
                $this->msgModel->setMessage($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }
}
