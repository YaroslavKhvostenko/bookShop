<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractOrderController;
use Models\ProjectModels\Session\User\SessionModel;
use Models\ProjectModels\OrderModel;
use Views\ProjectViews\OrderView;

class OrderController extends AbstractOrderController
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance(), new OrderModel(), new OrderView());
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function createAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

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

            if ($this->orderModel->isEmptyBasket()) {
                $this->msgModel->setMessage('empty', 'empty_basket');
                $this->prepareRedirect(
                    $this->createRedirectString('catalog', 'show')
                );
            } else {
                $this->orderView->render(
                    $this->orderView->getOptions('Оформление заказа', 'new_order.phtml')
                );
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, 'basket', 'show');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function newAction(array $params = null): void
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

            $this->getMsgModel('referer');
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be NULL!',
                    'order',
                    'create'
                );

                return;
            }

            if ($this->orderModel->isEmptyBasket()) {
                $this->msgModel->setMessage('empty', 'empty_basket');
                $this->prepareRedirect(
                    $this->createRedirectString('catalog', 'show')
                );

                return;
            }

            $result = $this->getDataValidator('referer')->emptyCheck($this->postInfo->getData());
            if (in_array('', $result)) {
                $this->checkResult($result, 'empty', '', 'order', 'create');
            } else {
                $result = $this->dataValidator->correctCheck($result);
                if (in_array('', $result)) {
                    $this->checkResult($result, 'wrong', '', 'order', 'create');
                } else {
                    $this->orderModel->setMessageModel($this->msgModel);
                    if ($this->orderModel->newOrder($result)) {
                        $this->redirectHome();
                    } else {
                        $this->prepareRedirect(
                            $this->createRedirectString('basket', 'show')
                        );
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->catchException($exception);
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
