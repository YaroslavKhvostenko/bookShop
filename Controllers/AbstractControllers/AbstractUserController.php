<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Validation\ImageValidator;
use Models\AbstractProjectModels\AbstractUserModel;
use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\Validation\Data\User\Validator;
use Models\AbstractProjectModels\Message\User\AbstractMsgModel;

abstract class AbstractUserController extends AbstractBaseController
{
    protected AbstractUserModel $userModel;
    protected AbstractDefaultView $userView;
    protected Validator $dataValidator;
    protected AbstractMsgModel $msgModel;
    protected ImageValidator $imageValidator;
    /**
     * Object for access to $_POST data
     */
    protected IDataManagement $postInfo;
    /**
     * Object for access to $_FILE data
     */
    protected IDataManagement $fileInfo;

    public function __construct(
        AbstractUserModel $userModel,
        AbstractDefaultView $userView,
        Validator $dataValidator,
        AbstractMsgModel $msgModel
    ) {
        $this->userModel = $userModel;
        $this->userView = $userView;
        $this->dataValidator = $dataValidator;
        $this->msgModel = $msgModel;
        $this->postInfo = DataRegistry::getInstance()->get('post');
        $this->fileInfo = DataRegistry::getInstance()->get('file');
        $this->imageValidator = new ImageValidator($this->fileInfo->getFileData());
    }

    /**
     * Render form for registration
     *
     * @param array|null $params
     * @return void
     */
    public function registrationAction(array $params = null): void
    {
        if (!$this->userModel->isSigned()) {
            $options = $this->userView->getOptions('Регистрация', 'registration.phtml');
            $this->userView->render($options);
        } else {
            $this->homeLocationByCustomerType();
        }
    }

    /**
     * Render form for authorization
     *
     * @return void
     */
    public function authorizationAction(): void
    {
        if (!$this->userModel->isSigned()) {
            $options = $this->userView->getOptions('Авторизация', 'login.phtml');
            $this->userView->render($options);
        } else {
            $this->homeLocationByCustomerType();
        }
    }

    /**
     * Create new user
     *
     * @param array|null $params
     * @return void
     */
    public function newAction(array $params = null): void
    {
        if ($this->userModel->isSigned()) {
            $this->homeLocationByCustomerType();
            return;
        }

        if (!$this->postInfo->isPost()) {
            $this->redirectHomeLocation();
            return;
        }

        $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getPostData(), 'registration');
        if (in_array(false, $emptyResult)) {
            $this->checkResult($emptyResult, 'empty_data', 'registration');
        } else {
            $correctResult = $this->dataValidator->correctCheck($this->postInfo->getPostData());
            if (in_array('', $correctResult)) {
                $this->checkResult($correctResult, 'wrong_data', 'registration');
            } else {
                if ($this->fileInfo->isImageSent() && !$this->imageValidator->validate($params[0])) {
                    $this->checkResult($this->imageValidator->getErrors(), 'wrong_data', 'registration');
                } else {
                    if (!$this->userModel->newUser($params[0], $correctResult)) {
                        $this->redirectLocation('user/registration');
                    } else {
                        $this->redirectHomeLocation();
                    }
                }
            }
        }
    }

    /**
     * Login action
     *
     * @return void
     */
    public function loginAction(array $params = null): void
    {
        if ($this->userModel->isSigned()) {
            $this->homeLocationByCustomerType();
            return;
        }

        if (!$this->postInfo->isPost()) {
            $this->redirectHomeLocation();
            return;
        }

        $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getPostData(), 'login');
        if (in_array(false, $emptyResult)) {
            $this->checkResult($emptyResult, 'empty_data','authorization');
        } else {
            if (!$this->userModel->login($params[0], $emptyResult)) {
                $this->redirectLocation('user/authorization');
            } else {
                $this->redirectHomeLocation();
            }
        }
    }

    protected function checkResult(array $result, string $messagesType, $actionType): void
    {
        foreach ($result as $field => $value) {
            if (!$value) {
                $this->msgModel->setMsg($this->msgModel->getMessage($messagesType, $field));
            }
        }
        $this->redirectLocation('user/' . $actionType);
    }

    /**
     * Logout action
     *
     * @return void
     */
    public function logoutAction(): void
    {
        if ($this->userModel->isSigned()) {
            $this->logoutActionType();
        } else {
            $this->redirectHomeLocation();
        }
    }

    abstract protected function homeLocationByCustomerType(): void;

    abstract protected function redirectHomeLocation(): void;

    abstract protected function logoutActionType(): void;
}
