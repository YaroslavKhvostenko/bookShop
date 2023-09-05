<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractIndexController;
use Models\ProjectModels\DefaultModel;
use Views\ProjectViews\DefaultView;

/**
 * @package Controllers\ProjectControllers
 */
class IndexController extends AbstractIndexController
{
    public function __construct()
    {
        parent::__construct(new DefaultModel(), new DefaultView());
    }

    protected function adminStatus(): bool
    {
        return $this->defaultModel->isAdmin();
    }

    protected function redirectLocation(string $url = null): void
    {
        $this->location('admin/');
    }
}
