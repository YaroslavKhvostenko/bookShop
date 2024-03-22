<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Head;

use Controllers\AbstractControllers\Admin\AbstractTaskController;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;
use Models\ProjectModels\HeadAdmin\TaskModel;
use Views\ProjectViews\HeadAdmin\TaskView;

class TaskController extends AbstractTaskController
{
    public function __construct()
    {
        parent::__construct(new TaskModel(), new TaskView(), SessionModel::getInstance());
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
                    'default', 'Params have to be NULL in createAction!', 'task', 'create'
                );

                return;
            }

            $this->taskModel->setMessageModel($this->msgModel);
            $data = $this->taskModel->getAdmins();
            $this->taskView->render(
                $this->taskView->getOptions('Создание задачи', 'create_task.phtml', $data)
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'admin', 'task');
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
                    'default', 'Params have to be NULL in newAction!', 'task', 'create'
                );

                return;
            }

            $data = $this->postInfo->getData();
            if (in_array('', $data)) {
                $this->checkResult($data, 'empty', '', 'task', 'create');

                return;
            }

            $data = $this->getDataValidator('referer')->checkData($data);
            if (in_array('', $data)) {
                $this->checkResult($data, 'wrong', '', 'task', 'create');

                return;
            }

            $this->taskModel->setMessageModel($this->msgModel);
            if (!$this->taskModel->newTask($data)) {
                $this->prepareRedirect($this->createRedirectString('admin', 'task'));
            } else {
                $this->prepareRedirect($this->createRedirectString('task', 'create'));
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, 'admin', 'task');
        }
    }

    protected function validateRequester(): bool
    {
        return (parent::validateRequester() || !$this->sessionModel->isHeadAdmin());
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('head/' . $url);
    }
}