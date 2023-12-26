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

    public function registrationAction(array $params = null): void
    {
        if ($this->userModel->isSigned()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($params !== null) {
                throw new \Exception('Params have to be empty in registrationAction!');
            }

            $this->userView->render($this->userView->getOptions('Регистрация', 'registration.phtml'));
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception);
        }
    }

    public function authorizationAction(array $params = null): void
    {
        if ($this->userModel->isSigned()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($params !== null) {
                throw new \Exception('Params have to be empty in authorizationAction!');
            }

            $this->userView->render($this->userView->getOptions('Авторизация', 'login.phtml'));
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception);
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
            if ($params !== null) {
                throw new \Exception('Params have to be empty in createAction!');
            }

            $this->dataValidator = $this->getDataValidator();
            $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getData());
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
                            $this->prepareRedirect($this->refController() . '/' . $this->refAction());
                        } else {
                            $this->redirectHome();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->refController(), $this->refAction());
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
            if ($params !== null) {
                throw new \Exception('Params have to be empty in loginAction!');
            }

            $this->dataValidator = $this->getDataValidator();
            $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getData());
            if (in_array(false, $emptyResult)) {
                $this->checkResult($emptyResult, self::EMPTY, $this->refAction());
            } else {
                $this->userModel->setMsgModel($this->msgModel);
                if (!$this->userModel->login($emptyResult)) {
                    $this->prepareRedirect($this->refController() . '/' . $this->refAction());
                } else {
                    $this->redirectHome();
                }
            }
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->refController(), $this->refAction());
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
        $this->prepareRedirect($this->refController() . '/' . $actionType);
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


    public function profileAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel();
            if ($params !== null) {
                throw new \Exception('Params have to be empty in profileAction!');
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
            $this->exceptionCatcher($exception);
        }
    }

    public function changeAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($params === null) {
                throw new \Exception('changeAction have to receive changing field from request URI string!');
            }

            $this->userModel->setMsgModel($this->msgModel);
            $result = $this->userModel->change($params[0]);
            $options = $this->userView->getOptions('Изменение данных', 'change_profile_item.phtml', $result);
            $this->userView->render($options);
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->reqController(), 'profile');
        }
    }

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
            if ($params === null) {
                throw new \Exception('updateAction have to receive changing field from request URI string!');
            } elseif ($params[0] === 'image') {
                $this->updateItemImage($params[0]);
            } else {
                switch ($params[0]) {
                    case 'login' :
                    case 'pass' :
                    case 'name' :
                    case 'birthdate' :
                    case 'email' :
                    case 'phone' :
                    case 'address' :
                        $this->updateItemText($params[0]);
                        break;
                    default :
                        throw new \Exception(
                            'Unknown field type :' . "'$params[0]'," .
                            ' in request string during updating user profile data!'
                        );
                }
            }

            $this->prepareRedirect($this->refController() . '/' . 'profile');
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->refController(), 'profile');
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function updateItemImage(string $fieldName): void
    {
        if (!array_key_exists($fieldName, $this->getFileInfo()->getFileData())) {
            throw new \Exception(
                'Different fileTypes in request URI string and incoming $_FILE. 
                Check \'change_profile_item.phtml\' or \'admin/change_profile_item.phtml\''
            );
        }

        if (!$this->fileInfo->isFileSent($fieldName)) {
            $this->msgModel->setMsg($this->msgModel->getMessage(self::EMPTY, $fieldName), $fieldName);

            return;
        }

        if (!$this->getImageValidator()->validate($this->refCustomer())) {
            $this->checkResult($this->imageValidator->getErrors(), self::WRONG, 'profile');
        } else {
            $this->userModel->setMsgModel($this->msgModel);
            $this->userModel->updateItemImage($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function updateItemText(string $fieldName): void
    {
        $postData = $this->postInfo->getData();
        if (!array_key_exists($fieldName, $postData)) {
            throw new \Exception(
                'Different fields in request URI string and incoming $_POST. 
                Check \'change_profile_item.phtml\' or \'admin/change_profile_item.phtml\''
            );
        }

        $this->dataValidator = $this->getDataValidator();
        $emptyResult = $this->dataValidator->emptyCheck($postData);
        if (in_array(false, $emptyResult)) {
            $this->checkResult($emptyResult, self::EMPTY, $this->refAction());
        } else {
            $correctResult = $this->dataValidator->correctCheck($emptyResult);
            if (in_array('', $correctResult)) {
                $this->checkResult($correctResult, self::WRONG, $this->refAction());
            } else {
                $this->userModel->setMsgModel($this->msgModel);
                $this->userModel->updateItemText($fieldName, $correctResult);
            }
        }
    }

    public function addAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($params === null) {
                throw new \Exception('addAction have to receive changing field from request URI string!');
            }

            if (!$this->addItemValidation(strtolower($params[0]))) {
                throw new \Exception(
                    'Wrong field in request URI string, 
                    during getting to page of a new profile item adding to user!'
                );
            }

            $field[strtolower($params[0])] = null;
            $options = $this->userView->getOptions('Добавление данных', 'add_profile_item.phtml', $field);
            $this->userView->render($options);
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->reqController(), 'profile');
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function addItemValidation(string $fieldName): bool
    {
        switch ($fieldName) {
            case 'image' :
            case 'text_file' : return true;
            default :
                return $this->addItemText($fieldName);
        }
    }

    protected function addItemText(string $fieldName): bool
    {
        return false;
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
            if ($params === null) {
                throw new \Exception('newAction have to receive field from request URI string!');
            } else {
                $this->newItemValidation(strtolower($params[0]));
            }

            $this->prepareRedirect($this->refController() . '/' . 'profile');
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->refController(), 'profile');
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
            if ($params === null) {
                throw new \Exception('deleteAction have to receive field from request URI string!');
            }

            if (!$this->deleteItemValidation(strtolower($params[0]))) {
                throw new \Exception(
                    'Wrong field in RequestUriString, 
                    during trying to delete profile item of user!'
                );
            }

            $this->userModel->setMsgModel($this->msgModel);
            $this->userModel->delete(strtolower($params[0]));
            $this->prepareRedirect($this->refController() . '/' . 'profile');
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->refController(), 'profile');
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function deleteItemValidation(string $fieldName): bool
    {
        switch ($fieldName) {
            case 'image' :
            case 'text_file' : $this->fileInfo = $this->getFileInfo();
                return true;
            default :
                return $this->deleteItemText($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function deleteItemText(string $fieldName): bool
    {
        return $this->addItemText($fieldName);
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function newItemValidation(string $fieldName)
    {
        switch ($fieldName) {
            case 'image' :
            case 'text_file' : $this->newItemFile($fieldName);
                break;
            default :
                 $this->newItemText($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function newItemFile(string $fieldName)
    {
        if (!array_key_exists($fieldName, $this->getFileInfo()->getFileData())) {
            throw new \Exception(
                'Different fileTypes in request URI string and incoming $_FILE. 
                Check \'change_profile_item.phtml\' or \'admin/change_profile_item.phtml\''
            );
        }

        if (!$this->fileInfo->isFileSent($fieldName)) {
            $this->msgModel->setMsg($this->msgModel->getMessage(self::EMPTY, $fieldName), $fieldName);

            return;
        }

        if (!$this->getImageValidator()->validate($this->refCustomer())) {
            $this->checkResult($this->imageValidator->getErrors(), self::WRONG, 'profile');
        } else {
            $this->userModel->setMsgModel($this->msgModel);
            $this->userModel->newItemFile($fieldName);
        }
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function newItemText(string $fieldName)
    {
        if (!$this->addItemText($fieldName)) {
            throw new \Exception('Wrong field in request URI string, 
                    during getting to page of a new profile item adding to user!'
            );
        }
    }

    /**
     * @return UserDataValidatorInterface|object|null
     * @throws \Exception
     */
    protected function getDataValidator()
    {
        if (!$this->dataValidator) {
            $this->dataValidator = FactoryValidator::getValidator($this->refCustomer(), $this->refAction());
        }

        return $this->dataValidator;
    }

    public function removeAction(array $params = null): void
    {
        if (!$this->userModel->isSigned() || $this->validateRequester()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($params === null) {
                throw new \Exception('removeAction have to receive changing field from request URI string!');
            }

            if (!$this->removeItemValidation(strtolower($params[0]))) {
                throw new \Exception(
                    'Wrong field in request URI string, 
                    during getting to page of a removing profile item of user!'
                );
            }

            $this->userModel->setMsgModel($this->msgModel);
            if (array_key_exists(strtolower($params[0]), $this->userModel->getUserSessionData())) {
                $data[strtolower($params[0])] = $this->userModel->getUserSessionData()[strtolower($params[0])];
                $options = $this->userView->getOptions('Удаление данных', 'remove_profile_item.phtml', $data);
                $this->userView->render($options);
            } else {
                throw new \Exception(
                    'Failure to get to remove_profile_item.phtml page,
                     because incoming field from RequestUriString, doesn\'t exist in user session data!
                    ');
            }
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception, $this->reqController(), 'profile');
        }
    }

    protected function removeItemValidation(string $fieldName): bool
    {
        return $this->addItemValidation($fieldName);
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
            case 'referer' : return $this->getMsgModelByReferer();
            case 'request' : return $this->getMsgModelByRequest();
            case 'default' : $this->msgModel = MsgModelsFactory::getMsgModel('default');
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

    protected function projectErrMsgSetter(string $errorType = null): void
    {
        $this->msgModel->errorMsgSetter($errorType);
    }

    protected function exceptionCatcher(
        \Exception $exception,
        string $controller = null,
        string $action = null,
        string $params = null
    ): void {
        if ($action !== null) {
            $action = '/' . $action;
        }

        if ($params !== null) {
            $params = '/' . $params;
        }

        $this->getLogger()->exceptionLog($exception);
        $this->projectErrMsgSetter();
        $this->prepareRedirect($controller . $action . $params);
    }

    abstract protected function validateRequester(): bool;

    abstract protected function redirectHome(): void;

    abstract protected function logoutByCustomerType(): void;
}
