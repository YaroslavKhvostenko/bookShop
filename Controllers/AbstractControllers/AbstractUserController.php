<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\File;
use Models\ProjectModels\Validation\ImageValidator;
use Models\AbstractProjectModels\AbstractUserModel;
use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
//use mysql_xdevapi\Exception;

abstract class AbstractUserController extends AbstractController
{
    protected const CONTROLLER_NAME = 'User_Controller';
    protected const REQUEST = 'request';
    protected const REFERER = 'referer';
    protected const EMPTY = 'empty';
    protected const WRONG = 'wrong';
    protected const IMAGE = 'image';
    protected AbstractUserModel $userModel;
    protected AbstractDefaultView $userView;
    protected ?ImageValidator $imageValidator = null;
    protected ?IDataManagement $fileInfo = null;

    public function __construct(
        AbstractUserModel $userModel,
        AbstractDefaultView $userView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($sessionModel);
        $this->userModel = $userModel;
        $this->userView = $userView;
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function registrationAction(array $params = null): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be empty in registrationAction!',
                    $this->getServerInfo()->getRequestController(),
                    $this->getServerInfo()->getRequestAction()
                );

                return;
            }

            $this->userView->render($this->userView->getOptions('Регистрация', 'registration.phtml'));
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function authorizationAction(array $params = null): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be empty in authorizationAction!',
                    $this->getServerInfo()->getRequestController(),
                    $this->getServerInfo()->getRequestAction()
                );

                return;
            }

            $this->userView->render($this->userView->getOptions('Авторизация', 'login.phtml'));
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function createAction(array $params = null): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be empty in createAction!',
                    $this->getServerInfo()->getRefererController(),
                    $this->getServerInfo()->getRefererAction()
                );

                return;
            }

            $emptyResult = $this->getDataValidator(self::REFERER)->emptyCheck($this->postInfo->getData());
            if (in_array(false, $emptyResult)) {
                $this->checkResult($emptyResult, self::EMPTY, $this->serverInfo->getRefererAction());
            } else {
                $correctResult = $this->dataValidator->correctCheck($emptyResult);
                if (in_array('', $correctResult)) {
                    $this->checkResult(
                        $correctResult, self::WRONG, $this->serverInfo->getRefererAction()
                    );
                } else {
                    if ($this->getFileInfo()->isFileSent(self::IMAGE) &&
                        !$this->getImageValidator()->validate($this->sessionModel->getUserType())) {
                        $this->checkResult(
                            $this->imageValidator->getErrors(),
                            self::WRONG,
                            $this->serverInfo->getRefererAction()
                        );
                    } else {
                        $this->userModel->setMessageModel($this->msgModel);
                        if (!$this->userModel->createUser($correctResult)) {
                            $this->prepareRedirect(
                                $this->createRedirectString(
                                    $this->serverInfo->getRefererController(),
                                    $this->serverInfo->getRefererAction()
                                )
                            );
                        } else {
                            $this->redirectHome();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->catchException(
                $exception,
                $this->getServerInfo()->getRefererController(),
                $this->getServerInfo()->getRefererAction()
            );
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function loginAction(array $params = null): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (!is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have to be empty in loginAction!',
                    $this->getServerInfo()->getRefererController(),
                    $this->getServerInfo()->getRefererAction()
                );

                return;
            }

            $emptyResult = $this->getDataValidator(self::REFERER)->emptyCheck($this->postInfo->getData());
            if (in_array(false, $emptyResult)) {
                $this->checkResult($emptyResult, self::EMPTY, $this->serverInfo->getRefererAction());
            } else {
                $this->userModel->setMessageModel($this->msgModel);
                if (!$this->userModel->login($emptyResult)) {
                    $this->prepareRedirect(
                        $this->createRedirectString(
                            $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction()
                        )
                    );
                } else {
                    $this->redirectHome();
                }
            }
        } catch (\Exception $exception) {
            $this->catchException(
                $exception,
                $this->getServerInfo()->getRefererController(),
                $this->getServerInfo()->getRefererAction()
            );
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

        $this->prepareRedirect(
            $this->createRedirectString(
                $this->getServerInfo()->getRefererController(), $actionType
            )
        );
    }

    public function logoutAction(): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $this->logoutByCustomerType();
        } else {
            $this->redirectHome();
        }
    }

    protected function redirectHomeByCustomerType(): void
    {
        if ($this->sessionModel->isAdmin()) {
            $this->redirect('admin/');
        } else {
            $this->redirect();
        }
    }

    private function getImageValidator(): ImageValidator
    {
        if (!$this->imageValidator) {
            $this->imageValidator = new ImageValidator($this->fileInfo->getFileData());
        }

        return $this->imageValidator;
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    protected function getFileInfo(): IDataManagement
    {
        if (!$this->fileInfo) {
            DataRegistry::getInstance()->register('file', new File\Manager());
            $this->fileInfo = DataRegistry::getInstance()->get('file');
        }

        return $this->fileInfo;
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function profileAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel();
            if (!is_null($params)) {
                $this->processWrongRequest('default', 'Params have to be empty in profileAction!');

                return;
            }

            $this->userModel->setMessageModel($this->msgModel);
            if (!$this->userModel->profile()) {
                $this->redirectHome();
            } else {
                $this->userView->render(
                    $this->userView->getOptions(
                        'Профиль',
                        'user_profile.phtml',
                        $this->userModel->getUser()
                    )
                );
            }
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function changeAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'changeAction have to receive changing field from request URI string!',
                    $this->serverInfo->getRequestController(),
                    'profile'
                );

                return;
            }

            $field = ImageValidator::validateFieldName(strtolower($params[0]));
            if ($field !== $params[0]) {
                $field = $this->getDataValidator(self::REQUEST)->validateFieldName(strtolower($params[0]));
            }

            $this->userModel->setMessageModel($this->msgModel);
            $result = $this->userModel->change($field);
            $options = $this->userView->getOptions('Изменение данных', 'change_profile_item.phtml', $result);
            $this->userView->render($options);
        } catch (\Exception $exception) {
            $this->catchException(
                $exception, $this->getServerInfo()->getRequestController(), 'profile'
            );
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function updateAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'updateAction have to receive changing field from request URI string!',
                    $this->serverInfo->getRefererController(),
                    'profile'
                );

                return;
            }

            if (!$this->getFileInfo()->isDataEmpty()) {
                $this->updateUserFileData(strtolower($params[0]));
            } else {
                $this->updateUserDataPost(strtolower($params[0]));
            }

            $this->prepareRedirect(
                $this->createRedirectString($this->serverInfo->getRefererController(), 'profile')
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->getServerInfo()->getRefererController(), 'profile');
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function updateUserFileData(string $fieldName): void
    {
        if ($this->updateUsingFileData($fieldName)) {
            $this->userModel->updateItemImage($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @return bool
     * @throws \Exception
     */
    protected function updateUsingFileData(string $fieldName): bool
    {
        $this->getImageValidator()->compareFieldNames($fieldName);
        if (!$this->fileInfo->isFileSent($fieldName)) {
            $this->msgModel->setMessage(self::EMPTY, $fieldName, $fieldName);

            return false;
        }

        if (!$this->getImageValidator()->validate($this->sessionModel->getUserType())) {
            $this->checkResult($this->imageValidator->getErrors(), self::WRONG, 'profile');

            return false;
        } else {
            $this->userModel->setMessageModel($this->msgModel);
        }

        return true;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function newUserFileData(string $fieldName): void
    {
        if ($this->updateUsingFileData($fieldName)) {
            $this->userModel->newItemFile($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function updateUserDataPost(string $fieldName): void
    {
        $result = $this->updateUsingPostData($fieldName);
        if (is_array($result)) {
            $this->userModel->updateItemText($fieldName, $result);
        }
    }

    /**
     * @param string $fieldName
     * @return array|null
     * @throws \Exception
     */
    protected function updateUsingPostData(string $fieldName): ?array
    {
        $postData = $this->postInfo->getData();
        $this->getDataValidator(self::REFERER)->compareFieldNames($fieldName, $postData);
        $emptyResult = $this->dataValidator->emptyCheck($postData);
        if (in_array(false, $emptyResult)) {
            $this->checkResult($emptyResult, self::EMPTY, $this->serverInfo->getRefererAction());
        } else {
            $correctResult = $this->dataValidator->correctCheck($emptyResult);
            if (in_array('', $correctResult)) {
                $this->checkResult($correctResult, self::WRONG, $this->serverInfo->getRefererAction());
            } else {
                $this->userModel->setMessageModel($this->msgModel);

                return $correctResult;
            }
        }

        return null;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function newUserDataPost(string $fieldName): void
    {
        $result = $this->updateUsingPostData($fieldName);
        if (is_array($result)) {
            $this->userModel->newItemText($fieldName, $result);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function addAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'addAction have to receive changing field from request URI string!',
                    $this->serverInfo->getRequestController(),
                    'profile'
                );

                return;
            }

            $param = ImageValidator::validateFieldName(strtolower($params[0]));
            if ($param !== $params[0]) {
                $param = $this->getDataValidator(self::REQUEST)->validateFieldName(strtolower($params[0]));
            }

            $field[strtolower($param)] = null;
            $options = $this->userView->getOptions('Добавление данных', 'add_profile_item.phtml', $field);
            $this->userView->render($options);
        } catch (\Exception $exception) {
            $this->catchException(
                $exception, $this->getServerInfo()->getRequestController(), 'profile'
            );
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function newAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'newAction have to receive changing field from request URI string!',
                    $this->serverInfo->getRefererController(),
                    'profile'
                );

                return;
            }

            if (!$this->getFileInfo()->isDataEmpty()) {
                $this->newUserFileData(strtolower($params[0]));
            } else {
                $this->newUserDataPost(strtolower($params[0]));
            }

            $this->prepareRedirect(
                $this->createRedirectString($this->serverInfo->getRefererController(), 'profile')
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->getServerInfo()->getRefererController(), 'profile');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function deleteAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'deleteAction have to receive changing field from request URI string!',
                    $this->serverInfo->getRefererController(),
                    'profile'
                );

                return;
            }

            if (ImageValidator::validateFieldName(strtolower($params[0])) === strtolower($params[0])) {
                $this->getFileInfo();
            } else {
                $params[0] = $this->getDataValidator('referer')->validateFieldName(strtolower($params[0]));
            }

            $this->userModel->setMessageModel($this->msgModel);
            $this->userModel->delete(strtolower($params[0]));
            $this->prepareRedirect(
                $this->createRedirectString($this->serverInfo->getRefererController(), 'profile')
            );
        } catch (\Exception $exception) {
            $this->catchException(
                $exception, $this->getServerInfo()->getRefererController(), 'profile'
            );
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function removeAction(array $params = null): void
    {
        if (!$this->sessionModel->isLoggedIn() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'removeAction have to receive changing field from request URI string!',
                    $this->serverInfo->getRequestController(),
                    'profile'
                );

                return;
            }

            $param = ImageValidator::validateFieldName(strtolower($params[0]));
            if ($param !== $params[0]) {
                $param = $this->getDataValidator(self::REQUEST)->validateFieldName(strtolower($params[0]));
            }

            $this->userModel->setMessageModel($this->msgModel);
            $data = $this->userModel->remove($param);
            if ($data !== null) {
                $options = $this->userView->getOptions('Удаление данных', 'remove_profile_item.phtml', $data);
                $this->userView->render($options);
            }
        } catch (\Exception $exception) {
            $this->catchException(
                $exception, $this->getServerInfo()->getRequestController(), 'profile'
            );
        }
    }

    abstract protected function validateRequester(): bool;

    abstract protected function redirectHome(): void;

    abstract protected function logoutByCustomerType(): void;
}
