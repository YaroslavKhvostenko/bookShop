<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Head;

use Controllers\ProjectControllers\Admin\IndexController as BaseController;

class IndexController extends BaseController
{
    public function indexAction(): void
    {
        $this->redirect('admin/');
    }
}
