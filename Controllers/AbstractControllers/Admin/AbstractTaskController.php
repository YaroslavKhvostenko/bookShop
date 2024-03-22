<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers\Admin;

use Controllers\AbstractControllers\AbstractController;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\AbstractProjectModels\Admin\AbstractTaskModel;
use Views\AbstractViews\Admin\AbstractTaskView;

abstract class AbstractTaskController extends AbstractController
{
    protected AbstractTaskModel $taskModel;
    protected AbstractTaskView $taskView;
    protected const CONTROLLER_NAME = 'Task_Controller';

    public function __construct(
        AbstractTaskModel $taskModel,
        AbstractTaskView $taskView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($sessionModel);
        $this->taskModel = $taskModel;
        $this->taskView = $taskView;
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
                $this->processWrongRequest(
                    'default', 'Params have to be NULL in updateAction!', 'admin', 'task'
                );

                return;
            }

            $result = $this->getDataValidator('request')->checkData($this->postInfo->getData());
            if (in_array('', $result)) {
                $this->checkResult($result, 'empty', '', 'admin', 'task');
            } else {
                $this->taskModel->setMessageModel($this->msgModel);
                $this->taskModel->updateTask($result);
                $this->prepareRedirect(
                    $this->createRedirectString('admin', 'task')
                );
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, 'admin', 'task');
        }
    }

    /**
     * @param array $result
     * @param string $messagesType
     * @param $checkType
     * @param string $controller
     * @param string $action
     * @param array|null $params
     * @throws \Exception
     */
    protected function checkResult(
        array $result,
        string $messagesType,
        $checkType,
        string $controller,
        string $action,
        array $params = null
    ): void {
        foreach ($result as $field => $value) {
            if ($value === $checkType) {
                $this->msgModel->setMessage($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }

    protected function redirectHome(): void
    {
        $this->redirect('admin/');
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }


    protected function validateRequester(): bool
    {
        return !$this->sessionModel->isAdmin();
    }
}
