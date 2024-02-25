<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Head;

use Controllers\AbstractControllers\AbstractAdminController;
use http\Exception\InvalidArgumentException;
use Models\ProjectModels\HeadAdmin\AdminModel;
use Views\ProjectViews\HeadAdmin\AdminView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;
use mysql_xdevapi\Exception;

class AdminController extends AbstractAdminController
{
    private const PAGE_TITLES = [
        'administrate' => 'Администрирование',
        'provide' => 'Предоставить доступ',
        'remove' => 'Снять доступ',
        'redirect' => 'Передать должность'
    ];
    private const PAGES = [
        'administrate' => 'administrate',
        'provide' => 'admin_access',
        'remove' => 'admin_access',
        'redirect' => 'admin_access',
        'approve' => 'administrate',
        'cancel' => 'administrate',
        'head' => 'administrate'
    ];

    public function __construct()
    {
        parent::__construct(new AdminModel(), new AdminView(), SessionModel::getInstance());
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function administrateAction(array $params = null)
    {
        try {
            $this->changePermission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function provideAction(array $params = null): void
    {
        try {
            $this->changePermission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function removeAction(array $params = null): void
    {
        try {
            $this->changePermission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function redirectAction(array $params = null): void
    {
        try {
            $this->changePermission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    protected function changePermission(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        $this->getMsgModel(self::REQUEST);
        if (!is_null($params)) {
            $this->processWrongRequest(
                'default',
                'Params have to be empty in ' . $this->serverInfo->getRequestAction() . 'Action!',
                $this->serverInfo->getRequestController(),
                $this->serverInfo->getRequestAction()
            );

            return;
        }

        $this->adminModel->setMessageModel($this->msgModel);
        $data = $this->adminModel->getAdmins($this->serverInfo->getRequestAction());
        $this->adminView->render(
            $this->adminView->getOptions(
                $this->getPageTitle($this->serverInfo->getRequestAction()),
                $this->getPage($this->serverInfo->getRequestAction()) . '.phtml',
                $data
            )
        );
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function accessAction(array $params = null): void
    {
        try {
            if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
                $this->redirectHomeByCustomerType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();
            }

            $this->getMsgModel(self::REFERER);
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be null in ' . $this->serverInfo->getRequestAction() . 'Action!',
                    $this->serverInfo->getRequestController(),
                    'administrate'
                );
            } else {
                $result = $this->getDataValidator(self::REFERER)->emptyCheckBox($this->postInfo->getData());
                if (in_array(false, $result)) {
                    $this->checkResult($result, self::EMPTY, $this->serverInfo->getRefererAction());
                } else {
                    $result = $this->dataValidator->correctCheckBox($result, $this->serverInfo->getRefererAction());
                    $this->adminModel->setMessageModel($this->msgModel);
                    $this->adminModel->changeAccess($result, $this->dataValidator->getFieldName());
                    $this->prepareRedirect(
                        $this->createRedirectString($this->serverInfo->getRefererController(), 'administrate')
                    );
                }
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, 'admin', 'administrate');
        }
    }

    /**
     * @param array $result
     * @param string $messagesType
     * @param $actionType
     * @throws \Exception
     */
    protected function checkResult(array $result, string $messagesType, $actionType): void
    {
        foreach ($result as $field => $value) {
            if (!$value) {
                $this->msgModel->setMessage($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($this->serverInfo->getRefererController(), $actionType));
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('head/'. $url);
    }

    protected function redirectHomeByCustomerType(): void
    {
        if ($this->sessionModel->isAdmin()) {
            parent::prepareRedirect();
        } else {
            $this->redirect();
        }
    }

    protected function validateRequester(): bool
    {
        return !$this->sessionModel->isAdmin() || !$this->sessionModel->isHeadAdmin();
    }

    private function getPageTitle(string $actionName): string
    {
        if (!array_key_exists($actionName, self::PAGE_TITLES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const FIELDS!'
            );
        }

        return self::PAGE_TITLES[$actionName];
    }

    private function getPage(string $actionName): string
    {
        if (!array_key_exists($actionName, self::PAGES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const FIELDS!'
            );
        }

        return self::PAGES[$actionName];
    }

    protected function redirectHome(): void
    {
        parent::prepareRedirect();
    }
}
