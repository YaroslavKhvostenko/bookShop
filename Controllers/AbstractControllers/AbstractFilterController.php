<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\AbstractProjectModels\AbstractFilterModel;
use Interfaces\IDataManagement;
use Models\ProjectModels\Get;

abstract class AbstractFilterController extends AbstractController
{
    protected const CONTROLLER_NAME = 'Filter_Controller';
    protected AbstractFilterModel $filterModel;
    protected ?IDataManagement $getInfo = null;

    public function __construct(AbstractFilterModel $filterModel, AbstractSessionModel $sessionModel)
    {
        parent::__construct($sessionModel);
        $this->filterModel = $filterModel;
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function setAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->receiveGetInfo()->isGet()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'setAction() incoming variable $params has to be NULL! Check what comes from URI string!',
                    $this->serverInfo->getRefererController(),
                    $this->serverInfo->getRefererAction()
                );

                return;
            }

            $this->getDataValidator('request')->setControllerName($this->serverInfo->getRefererController());
            $this->dataValidator->setActionName($this->serverInfo->getRefererAction());
            $result = $this->dataValidator->emptyCheck($this->getInfo->getData());
            if (in_array('', $result)) {
                $this->checkResult(
                    $result,
                    'empty',
                    '',
                    $this->serverInfo->getRefererController(),
                    $this->serverInfo->getRefererAction(),
                );
            } else {
                $result = $this->dataValidator->correctCheck($result);
                if (in_array('', $result)) {
                    $this->checkResult(
                        $result,
                        'wrong',
                        '',
                        $this->serverInfo->getRefererController(),
                        $this->serverInfo->getRefererAction(),
                    );
                } else {
                    $this->filterModel->setFilter(
                        $result, $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction()
                    );

                    $this->prepareRedirect(
                        $this->createRedirectString(
                            $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction()
                        )
                    );
                }
            }
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
    public function clearAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->receiveGetInfo()->isGet()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'clearAction() incoming variable $params has to be NULL! Check what comes from URI string!',
                    $this->getServerInfo()->getRefererController(),
                    $this->serverInfo->getRefererAction()
                );

                return;
            }

            $result = $this->getDataValidator('request')->emptyCheck($this->getInfo->getData());
            if (!$result) {
                $this->msgModel->setMessage('empty', 'empty_clear_filter_check_mark');
            } else {
                $this->filterModel->setMessageModel($this->msgModel);
                $this->filterModel->clearFilter(
                    $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction()
                );
            }

            $this->prepareRedirect(
                $this->createRedirectString(
                    $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction()
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

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }

    protected function receiveGetInfo(): IDataManagement
    {
        if (!$this->getInfo) {
            $this->getInfo = new Get\Manager();
        }

        return $this->getInfo;
    }
}
