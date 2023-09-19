<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\File;
use Models\ProjectModels\Post;
use Models\ProjectModels\Validation\ImageValidator;
use Models\AbstractProjectModels\AbstractUserModel;
use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\Validation\Data\User\Validator;
use Models\AbstractProjectModels\Message\User\AbstractMsgModel;
use Models\ProjectModels\Logger;

abstract class AbstractUserController extends AbstractBaseController
{
    protected AbstractUserModel $userModel;
    protected AbstractDefaultView $userView;
    protected Validator $dataValidator;
    protected AbstractMsgModel $msgModel;
    protected ?ImageValidator $imageValidator = null;
    protected ?IDataManagement $postInfo = null;
    protected ?IDataManagement $fileInfo = null;
    protected Logger $logger;

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
        $this->logger = new Logger();
    }

    public function registrationAction(array $params = null): void
    {
        if (!$this->userModel->isSigned()) {
            $options = $this->userView->getOptions('Регистрация', 'registration.phtml');
            $this->userView->render($options);
        } else {
            $this->redirectHomeByCustomerType();
        }
    }

    public function authorizationAction(): void
    {
        if (!$this->userModel->isSigned()) {
            $options = $this->userView->getOptions('Авторизация', 'login.phtml');
            $this->userView->render($options);
        } else {
            $this->redirectHomeByCustomerType();
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function newAction(array $params = null): void
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
            $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getPostData());
            if (in_array(false, $emptyResult)) {
                $this->checkResult($emptyResult, 'empty_data', 'registration');
            } else {
                $correctResult = $this->dataValidator->correctCheck($emptyResult);
                if (in_array('', $correctResult)) {
                    $this->checkResult($correctResult, 'wrong_data', 'registration');
                } else {
                    if ($this->getFileInfo()->isImageSent() && !$this->getImageValidator()->validate($params[0])) {
                        $this->checkResult($this->imageValidator->getErrors(), 'wrong_data', 'registration');
                    } else {
                        if (!$this->userModel->newUser($params[0], $correctResult)) {
                            $this->prepareRedirect('user/registration');
                        } else {
                            $this->redirectHome();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->log('default', $exception->getMessage() . $exception->getTraceAsString());
            $this->msgModel->setMsg($this->msgModel->getMessage('registration', 'project_mistake'));
            $this->prepareRedirect('user/registration');
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
        $emptyResult = $this->dataValidator->emptyCheck($this->postInfo->getPostData());
        if (in_array(false, $emptyResult)) {
            $this->checkResult($emptyResult, 'empty_data','authorization');
        } else {
            if (!$this->userModel->login($params[0], $emptyResult)) {
                $this->prepareRedirect('user/authorization');
            } else {
                $this->redirectHome();
            }
        }
        } catch (\Exception $exception) {
            $this->logger->log('default', $exception->getMessage() . $exception->getTraceAsString());
            $this->msgModel->setMsg($this->msgModel->getMessage('login', 'project_mistake'));
            $this->prepareRedirect('user/authorization');
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
                $this->msgModel->setMsg($this->msgModel->getMessage($messagesType, $field));
            }
        }
        $this->prepareRedirect('user/' . $actionType);
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

    abstract protected function redirectHome(): void;

    abstract protected function logoutByCustomerType(): void;
}
