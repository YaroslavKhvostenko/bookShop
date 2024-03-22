<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers\Admin;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Views\AbstractViews\Admin\AbstractAdminView;
use Models\AbstractProjectModels\Admin\AbstractAdminModel;
use Controllers\AbstractControllers\AbstractController;

abstract class AbstractAdminController extends AbstractController
{
    protected AbstractAdminModel $adminModel;
    protected AbstractAdminView $adminView;
    protected const CONTROLLER_NAME = 'Admin_Controller';

    public function __construct(
        AbstractAdminModel $adminModel,
        AbstractAdminView $adminView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($sessionModel);
        $this->adminModel = $adminModel;
        $this->adminView = $adminView;
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }

    protected function redirectHome(): void
    {
        $this->redirect('admin/');
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function taskAction(array $params = null): void
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
                    'Params have to be NULL in taskAction!'
                );

                return;
            }

            $this->adminModel->setMessageModel($this->msgModel);
            $data = $this->adminModel->getTasks();
            $this->adminView->render(
                $this->adminView->getOptions('Задачи', 'tasks.phtml', $data)
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'catalog', 'show');
        }
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

    abstract protected function validateRequester(): bool;
}
