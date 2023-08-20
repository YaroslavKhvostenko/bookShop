<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Models\ProjectModels\DefaultModel;
use Views\DefaultView;

/**
 * @package Controllers\ProjectControllers
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
        if ($this->defaultModel->isSigned() && $this->defaultModel->isAdmin()) {
            $this->adminHomeLocation();
        } else {
            if ($this->defaultModel->isSigned()) {
                $content = 'user_main.phtml';
            } else {
                $content = 'main.phtml';
            }
            $options = $this->defaultView->getOptions('Главная', $content);
            $this->defaultView->render($options);
        }
    }
}
