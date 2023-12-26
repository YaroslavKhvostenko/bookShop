<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\UserModel;
use Views\ProjectViews\UserView;
use Controllers\AbstractControllers\AbstractUserController;

class UserController extends AbstractUserController
{
    public function __construct()
    {
        parent::__construct(new UserModel(), new UserView());
    }

    protected function redirectHome(): void
    {
        $this->redirect();
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }


    protected function logoutByCustomerType(): void
    {
        if (!$this->userModel->isAdmin()) {
            $this->userModel->logout();
            $this->redirectHome();
        } else {
            $this->redirect('admin/');
        }
    }

    protected function validateRequester(): bool
    {
        return $this->userModel->isAdmin();
    }

    /**
     * @param string $fieldName
     * @return bool
     * @throws \Exception
     */
    protected function addItemText(string $fieldName): bool
    {
        switch ($fieldName) {
            case 'phone' :
            case 'address' : return true;
            default : return parent::addItemText($fieldName);
        }
    }

    protected function deleteItemText(string $fieldName): bool
    {
        return $this->addItemText($fieldName);
    }

    protected function newItemText(string $fieldName)
    {
        if (!$this->addItemText($fieldName)) {
            parent::newItemText($fieldName);
        }

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
                $this->userModel->newItemText($fieldName, $correctResult);
            }
        }
    }
}
