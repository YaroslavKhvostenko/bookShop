<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Interfaces\User\UserDataValidatorInterface;
use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\File;
use Models\ProjectModels\Message\User\MsgModelsFactory;
use Models\ProjectModels\Post;
use Models\ProjectModels\Validation\Data\FactoryValidator;
use Models\ProjectModels\Validation\ImageValidator;
use Models\AbstractProjectModels\AbstractUserModel;
use Views\AbstractViews\AbstractDefaultView;
//use mysql_xdevapi\Exception;

abstract class AbstractUserController extends AbstractBaseController
{
    protected const REQUEST = 'request';
    protected const REFERER = 'referer';
    protected const CUSTOMER = 'customer';
    protected const CONTROLLER = 'controller';
    protected const ACTION = 'action';
    protected const EMPTY = 'empty';
    protected const WRONG = 'wrong';
    protected const IMAGE = 'image';

    protected AbstractUserModel $userModel;
    protected AbstractDefaultView $userView;
    protected ?IDataManagement $serverInfo = null;
    protected ?UserDataValidatorInterface $dataValidator = null;
    protected ?AbstractBaseMsgModel $msgModel = null;
    protected ?ImageValidator $imageValidator = null;
    protected ?IDataManagement $postInfo = null;
    protected ?IDataManagement $fileInfo = null;

    public function __construct(
        AbstractUserModel $userModel,
        AbstractDefaultView $userView
    ) {
        $this->userModel = $userModel;
        $this->userView = $userView;
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function registrationAction(array $params = null): void
    {
        if ($this->userModel->isSigned()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (!$this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'Params have to be empty in registrationAction!',
                    $this->reqController(),
                    $this->reqAction()
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
        if ($this->userModel->isSigned()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (!$this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'Params have to be empty in authorizationAction!',
                    $this->reqController(),
                    $this->reqAction()
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
        if ($this->userModel->isSigned()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (!$this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'Params have to be empty in createAction!',
                    $this->refController(),
                    $this->refAction()
                );

                return;
            }

            $emptyResult = $this->getDataValidator(self::REFERER)->emptyCheck($this->postInfo->getData());
            if (in_array(false, $emptyResult)) {
                $this->checkResult($emptyResult, self::EMPTY, $this->refAction());
            } else {
                $correctResult = $this->dataValidator->correctCheck($emptyResult);
                if (in_array('', $correctResult)) {
                    $this->checkResult($correctResult, self::WRONG, $this->refAction());
                } else {
                    if ($this->getFileInfo()->isFileSent(self::IMAGE) &&
                        !$this->getImageValidator()->validate($this->refCustomer())) {
                        $this->checkResult($this->imageValidator->getErrors(), self::WRONG, $this->refAction());
                    } else {
                        $this->userModel->setMsgModel($this->msgModel);
                        if (!$this->userModel->createUser($correctResult)) {
                            $this->prepareRedirect($this->createRedirectString($this->refController(), $this->refAction()));
                        } else {
                            $this->redirectHome();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->refController(), $this->refAction());
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function loginAction(array $params = null): void
    {
        if ($this->userModel->isSigned()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (!$this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'Params have to be empty in loginAction!',
                    $this->refController(),
                    $this->refAction()
                );

                return;
            }

            $emptyResult = $this->getDataValidator(self::REFERER)->emptyCheck($this->postInfo->getData());
            if (in_array(false, $emptyResult)) {
                $this->checkResult($emptyResult, self::EMPTY, $this->refAction());
            } else {
                $this->userModel->setMsgModel($this->msgModel);
                if (!$this->userModel->login($emptyResult)) {
                    $this->prepareRedirect($this->createRedirectString($this->refController(), $this->refAction()));
                } else {
                    $this->redirectHome();
                }
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->refController(), $this->refAction());
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
                $this->msgModel->setMsg($this->msgModel->getMessage($messagesType, $field), $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($this->refController(), $actionType));
    }

    public function logoutAction(): void
    {
        if ($this->userModel->isSigned()) {
            $this->logoutByCustomerType();
        } else {
            $this->redirectHome();
        }
    }

    protected function redirectHomeByCustomerType(): void
    {
        if ($this->userModel->isAdmin()) {
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
    private function getFileInfo(): IDataManagement
    {
        if (!$this->fileInfo) {
            DataRegistry::getInstance()->register('file', new File\Manager());
            $this->fileInfo = DataRegistry::getInstance()->get('file');
        }

        return $this->fileInfo;
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    private function getPostInfo(): IDataManagement
    {
        if (!$this->postInfo) {
            DataRegistry::getInstance()->register('post', new Post\Manager());
            $this->postInfo = DataRegistry::getInstance()->get('post');
        }

        return $this->postInfo;
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function profileAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel();
            if (!$this->paramsIsNull($params)) {
                $this->paramsErrorResult('default', 'Params have to be empty in profileAction!');

                return;
            }

            $this->userModel->setMsgModel($this->msgModel);
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
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'changeAction have to receive changing field from request URI string!',
                    $this->reqController(),
                    'profile'
                );

                return;
            }

            $field = ImageValidator::validateFieldName(strtolower($params[0]));
            if ($field !== $params[0]) {
                $field = $this->getDataValidator(self::REQUEST)->validateFieldName(strtolower($params[0]));
            }

            $this->userModel->setMsgModel($this->msgModel);
            $result = $this->userModel->change($field);
            $options = $this->userView->getOptions('Изменение данных', 'change_profile_item.phtml', $result);
            $this->userView->render($options);
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->reqController(), 'profile');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function updateAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if ($this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'updateAction have to receive changing field from request URI string!',
                    $this->refController(),
                    'profile'
                );

                return;
            }

            if (!$this->getFileInfo()->isDataEmpty()) {
                $this->updateUserFileData(strtolower($params[0]));
            } else {
                $this->updateUserDataPost(strtolower($params[0]));
            }

            $this->prepareRedirect($this->createRedirectString($this->refController(), 'profile'));
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->refController(), 'profile');
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
            $this->msgModel->setMsg($this->msgModel->getMessage(self::EMPTY, $fieldName), $fieldName);

            return false;
        }

        if (!$this->getImageValidator()->validate($this->refCustomer())) {
            $this->checkResult($this->imageValidator->getErrors(), self::WRONG, 'profile');

            return false;
        } else {
            $this->userModel->setMsgModel($this->msgModel);
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
            $this->checkResult($emptyResult, self::EMPTY, $this->refAction());
        } else {
            $correctResult = $this->dataValidator->correctCheck($emptyResult);
            if (in_array('', $correctResult)) {
                $this->checkResult($correctResult, self::WRONG, $this->refAction());
            } else {
                $this->userModel->setMsgModel($this->msgModel);

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
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'addAction have to receive changing field from request URI string!',
                    $this->reqController(),
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
            $this->catchException($exception, $this->reqController(), 'profile');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function newAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if ($this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'newAction have to receive changing field from request URI string!',
                    $this->refController(),
                    'profile'
                );

                return;
            }

            if (!$this->getFileInfo()->isDataEmpty()) {
                $this->newUserFileData(strtolower($params[0]));
            } else {
                $this->newUserDataPost(strtolower($params[0]));
            }

            $this->prepareRedirect($this->createRedirectString($this->refController(), 'profile'));
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->refController(), 'profile');
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function deleteAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if ($this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'deleteAction have to receive changing field from request URI string!',
                    $this->refController(),
                    'profile'
                );

                return;
            }

            if (ImageValidator::validateFieldName(strtolower($params[0])) === strtolower($params[0])) {
                $this->getFileInfo();
            } else {
                $params[0] = $this->getDataValidator('referer')->validateFieldName(strtolower($params[0]));
            }

            $this->userModel->setMsgModel($this->msgModel);
            $this->userModel->delete(strtolower($params[0]));
            $this->prepareRedirect($this->createRedirectString($this->refController(), 'profile'));
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->refController(), 'profile');
        }
    }

    /**
     * @return UserDataValidatorInterface|null
     * @throws \Exception
     */
    protected function getDataValidator(string $uriType): UserDataValidatorInterface
    {
        if (!$this->dataValidator) {
            switch (strtolower($uriType)) {
                case 'request' :
                    $this->dataValidator = FactoryValidator::getValidator($this->reqCustomer(), $this->reqAction());
                    break;
                case 'referer' :
                    $this->dataValidator = FactoryValidator::getValidator($this->refCustomer(), $this->refAction());
                    break;
                default :
                    throw new \Exception('Wrong URI type declaration for creation of DataValidator');
            }
        }

        return $this->dataValidator;
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function removeAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($this->paramsIsNull($params)) {
                $this->paramsErrorResult(
                    'default',
                    'removeAction have to receive changing field from request URI string!',
                    $this->reqController(),
                    'profile'
                );

                return;
            }

            $param = ImageValidator::validateFieldName(strtolower($params[0]));
            if ($param !== $params[0]) {
                $param = $this->getDataValidator(self::REQUEST)->validateFieldName(strtolower($params[0]));
            }

            $this->userModel->setMsgModel($this->msgModel);
            $data = $this->userModel->remove($param);
            if ($data !== null) {
                $options = $this->userView->getOptions('Удаление данных', 'remove_profile_item.phtml', $data);
                $this->userView->render($options);
            }
        } catch (\Exception $exception) {
            $this->catchException($exception, $this->reqController(), 'profile');
        }
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
        if (!$this->serverInfo) {
            $this->getServerInfo()->initializeServerUriOptions(self::REFERER);
        }

        return $this->serverInfo->getRefererOption($option);
    }

    /**
     * @param string $option
     * @return string
     * @throws \Exception
     */
    protected function getRequestOption(string $option): string
    {
        if (!$this->serverInfo) {
            $this->getServerInfo()->initializeServerUriOptions(self::REQUEST);
        }

        return $this->serverInfo->getRequestOption($option);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function reqCustomer(): string
    {
        return $this->getRequestOption(self::CUSTOMER);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function reqController(): string
    {
        return $this->getRequestOption(self::CONTROLLER);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function reqAction(): string
    {
        return $this->getRequestOption(self::ACTION);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function refCustomer(): string
    {
        return $this->getRefererOption(self::CUSTOMER);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function refController(): string
    {
        return $this->getRefererOption(self::CONTROLLER);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function refAction(): string
    {
        return $this->getRefererOption(self::ACTION);
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByReferer(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MsgModelsFactory::getMsgModel($this->refCustomer(), $this->refAction());
        }

        return $this->msgModel;
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByRequest(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MsgModelsFactory::getMsgModel($this->reqCustomer(), $this->reqAction());
        }

        return $this->msgModel;
    }

    /**
     * @param string|null $errorType
     * @throws \Exception
     */
    protected function errMsgSetter(string $errorType = null): void
    {
        $this->msgModel->errorMsgSetter($errorType);
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
        $this->errMsgSetter();
        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }

    abstract protected function validateRequester(): bool;

    abstract protected function redirectHome(): void;

    abstract protected function logoutByCustomerType(): void;

    /**
     * @param $params
     * @return bool
     */
    protected function paramsIsNull($params): bool
    {
        return $params === null;
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
    protected function paramsErrorResult(
        string $logFileType,
        string $logMsg,
        string $controller = null,
        string $action = null,
        string $params = null
    ): void {
        $this->getLogger()->log($logFileType, $logMsg);
        $this->errMsgSetter();
        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }
}
