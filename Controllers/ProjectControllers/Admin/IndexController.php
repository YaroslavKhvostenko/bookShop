<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Controllers\AbstractControllers\AbstractIndexController;
use Models\ProjectModels\Admin\DefaultModel;
use Views\ProjectViews\Admin\DefaultView;

class IndexController extends AbstractIndexController
{
    public function __construct()
    {
        parent::__construct(new DefaultModel(), new DefaultView());
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect();
    }

    protected function validateRequest(): bool
    {
        return $this->defaultModel->isSigned() && !$this->defaultModel->isAdmin();
    }
}
