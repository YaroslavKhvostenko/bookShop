<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractIndexController;
use Models\ProjectModels\DefaultModel;
use Views\ProjectViews\DefaultView;

class IndexController extends AbstractIndexController
{
    public function __construct()
    {
        parent::__construct(new DefaultModel(), new DefaultView());
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect('admin/');
    }

    protected function validateRequest(): bool
    {
        return $this->defaultModel->isSigned() && $this->defaultModel->isAdmin();
    }
}
