<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Views\AbstractViews\Admin\AbstractAdminView;
use Models\AbstractProjectModels\Admin\AbstractAdminModel;

abstract class AbstractAdminController extends AbstractController
{
    protected AbstractAdminModel $adminModel;
    protected AbstractAdminView $adminView;
    protected const CONTROLLER_NAME = 'Admin_Controller';
    protected const REQUEST = 'request';
    protected const REFERER = 'referer';
    protected const EMPTY = 'empty';

    public function __construct(
        AbstractAdminModel $adminModel,
        AbstractAdminView $adminView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($sessionModel);
        $this->adminModel = $adminModel;
        $this->adminView = $adminView;
    }

    protected function prepareRedirect(string $url = null): void
    {
        if ($this->sessionModel->isAdmin()) {
            $this->redirect($url);
        } else {
            $this->redirect();
        }
    }

    abstract protected function validateRequester(): bool;

    abstract protected function redirectHomeByCustomerType(): void;

    abstract protected function redirectHome(): void;

}
