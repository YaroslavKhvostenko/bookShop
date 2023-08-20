<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\UserModel;
use Views\UserView;
use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Validation\Data\User\Validator;
use Models\ProjectModels\Message\User\ResultMessageModel;

/**
 * @package Controllers\ProjectControllers
 */
class UserController extends BaseController
{
    private UserModel $userModel;

    private UserView $userView;

    private Validator $dataValidator;

    private ResultMessageModel $msgModel;

    /**
     * Object for access to $_POST data
     */
    private IDataManagement $postInfo;

    /**
     * Object for access to $_FILE data
     */
    private IDataManagement $fileInfo;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
        $this->postInfo = DataRegistry::getInstance()->get('post');

        if ($this->postInfo->isPost()) {
            $this->dataValidator = new Validator();
            $this->msgModel = new ResultMessageModel();
            $this->fileInfo = DataRegistry::getInstance()->get('file');
        }
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
     * Create new user
     *
     * @param array|null $params
     * @return void
     */
    public function newAction(array $params = null): void
    {
        if (!$this->userModel->isSigned()) {
            if ($this->postInfo->isPost()) {
                $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getPostData(), 'registration');
                if (!in_array(false, $emptyResult)) {
                    $correctResult = $this->dataValidator->correctCheck($this->postInfo->getPostData());
                    if (!in_array('', $correctResult)) {
                        if ($this->fileInfo->isImageSent()) {
                            if ($this->fileInfo->isImageType()) {
                                if ($this->fileInfo->isSizeCorrect($params[0])) {
                                    if (!$this->userModel->newUser($params[0], $correctResult)) {
                                        $this->location('/user/registration');
                                    } else {
                                        $this->homeLocation();
                                    }
                                } else {
                                    $this->msgModel->setMsg($this->msgModel->notCorrectData('image_size'));
                                    $this->location('/user/registration');
                                }
                            } else {
                                $this->msgModel->setMsg($this->msgModel->notCorrectData('image_type'));
                                $this->location('/user/registration');
                            }
                        } else {
                            if (!$this->userModel->newUser($params[0], $correctResult)) {
                                $this->location('/user/registration');
                            } else {
                                $this->homeLocation();
                            }
                        }
                    } else {
                        foreach ($correctResult as $key => $value) {
                            if (!$value) {
                                $this->msgModel->setMsg($this->msgModel->notCorrectData($key));
                            }
                        }
                        $this->location('/user/registration');
                    }
                } else {
                    foreach ($emptyResult as $key => $value) {
                        if (!$value) {
                            $this->msgModel->setMsg($this->msgModel->emptyDataMsg($key));
                        }
                    }
                    $this->location('/user/registration');
                }
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocationByCustomerType();
        }
    }

    /**
     * if user is logged sends to homeLocation depending on if it is a simple user or admin user
     */
    public function homeLocationByCustomerType(): void
    {
        if ($this->userModel->isAdmin()) {
            $this->adminHomeLocation();
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Logout action
     *
     * @return void
     */
    public function logoutAction(): void
    {
        if ($this->userModel->isSigned()) {
            if ($this->userModel->isAdmin()) {
                $this->adminHomeLocation();
            } else {
                $this->userModel->logout();
                $this->homeLocation();
            }
        } else {
            $this->homeLocation();
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
     * Login action
     *
     * @return void
     */
    public function loginAction(array $params = null): void
    {
        if (!$this->userModel->isSigned()) {
            if ($this->postInfo->isPost()) {
                $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getPostData(), 'login');
                if (!in_array(false, $emptyResult)) {
                    if (!$this->userModel->login($params[0], $emptyResult)) {
                        $this->location('/user/authorization');
                    } else {
                        $this->homeLocation();
                    }
                } else {
                    foreach ($emptyResult as $key => $value) {
                        if (!$value) {
                            $this->msgModel->setMsg($this->msgModel->emptyDataMsg($key));
                        }
                    }
                    $this->location('/user/authorization');
                }
            } else {
                $this->homeLocation();
            }
        } else {
            $this->homeLocationByCustomerType();
        }
    }
}
