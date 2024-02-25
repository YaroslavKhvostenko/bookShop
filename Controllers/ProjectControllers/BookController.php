<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractBookController;
use Models\ProjectModels\BookModel;
use Views\ProjectViews\BookView;
use Models\ProjectModels\Session\User\SessionModel;

class BookController extends AbstractBookController
{
    public function __construct()
    {
        parent::__construct(new BookModel(), new BookView(), SessionModel::getInstance());
    }

    protected function validateRequest(): bool
    {
        return $this->sessionModel->isAdmin();
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect();
    }

    protected function redirectHome(): void
    {
        if ($this->sessionModel->isAdmin()) {
            $this->prepareRedirect('admin/');
        } else {
            $this->prepareRedirect();
        }
    }

    public function addAction(array $params = null): void
    {
        // TODO: Implement addAction() method.
    }

    protected function redirectHomeByCustomerType(): void
    {
        // TODO: Implement redirectHomeByCustomerType() method.
    }
}
