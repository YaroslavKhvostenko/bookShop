<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\AbstractDefaultModel;

abstract class AbstractIndexController extends AbstractBaseController
{
    protected AbstractDefaultModel $defaultModel;
    protected AbstractDefaultView $defaultView;

    public function __construct(AbstractDefaultModel $defaultModel, AbstractDefaultView $defaultView)
    {
        $this->defaultModel = $defaultModel;
        $this->defaultView = $defaultView;
    }

    public function indexAction(): void
    {
        if ($this->validateRequest()) {
            $this->prepareRedirect();
        } else {
            if ($this->defaultModel->isSigned()) {
                $content = 'user_main.phtml';
            } else {
                $content = 'main.phtml';
            }
        }

        $options = $this->defaultView->getOptions('Главная', $content);
        $this->defaultView->render($options);
    }

    abstract protected function validateRequest(): bool;
}
