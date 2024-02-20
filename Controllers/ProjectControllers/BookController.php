<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractBookController;
use Models\ProjectModels\BookModel;
use Views\ProjectViews\BookView;

class BookController extends AbstractBookController
{
    public function __construct()
    {
        parent::__construct(new BookModel(), new BookView());
    }

    protected function validateRequest(): bool
    {
        return $this->bookModel->getSessModel()->isAdmin();
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect();
    }

    protected function redirectHome(): void
    {
        if ($this->bookModel->getSessModel()->isAdmin()) {
            $this->prepareRedirect('admin/');
        } else {
            $this->prepareRedirect();
        }
    }

    public function addAction(array $params = null): void
    {
        // TODO: Implement addAction() method.
    }
}
