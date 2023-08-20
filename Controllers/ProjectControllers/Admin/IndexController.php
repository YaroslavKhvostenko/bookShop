<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Models\ProjectModels\DefaultModel;
use Views\DefaultView;
use Controllers\ProjectControllers\BaseController;

/**
 * @package Controllers\ProjectControllers\Admin
 */
class IndexController extends BaseController
{
    private DefaultView $defaultView;

    private DefaultModel $defaultModel;

    public function __construct()
    {
        $this->defaultView = new DefaultView();
        $this->defaultModel = new DefaultModel();
    }

    /**
     * Render default page
     *
     * @return void
     */
    public function indexAction(): void
    {
        if ($this->defaultModel->isSigned() && !$this->defaultModel->isAdmin()) {
            $this->homeLocation();
        } else {
            if ($this->defaultModel->isSigned()) {
                $content = 'admin/admin_main.phtml';
            } else {
                $content = 'admin/main.phtml';
            }
            $options = $this->defaultView->getOptions('Главная', $content);
            $this->defaultView->render($options);
        }
//        $content = $this->sessionInfo->getUser() !== null ? 'admin/admin_main.phtml' : 'admin/main.phtml';
//        $options = $this->defaultView->getOptions('Главная', $content);
//        $this->defaultView->render($options);
    }
}
