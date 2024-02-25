<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Views\AbstractViews\AbstractDefaultView;
use Models\AbstractProjectModels\AbstractDefaultModel;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;

abstract class AbstractIndexController extends AbstractBaseController
{
    protected AbstractDefaultView $defaultView;
    protected AbstractSessionModel $sessionModel;
    protected ?AbstractDefaultModel $defaultModel = null;

    public function __construct(
        AbstractDefaultView $defaultView,
        AbstractSessionModel $sessionModel,
        AbstractDefaultModel $defaultModel = null
    ) {
        parent::__construct($sessionModel);
        $this->defaultView = $defaultView;
        $this->defaultModel = $defaultModel;
    }

    /**
     * @throws \Exception
     */
    public function indexAction(): void
    {
        try {
            if ($this->validateRequest()) {
                $this->prepareRedirect();
            } else {
                $phtml = '.phtml';
                $content = 'main';
                $title = 'Главная';
                if ($this->sessionModel->isLoggedIn()) {
                    $content = 'user_main';
                    $title = 'Личный кабинет';
                }
            }

            $options = $this->defaultView->getOptions($title, $content . $phtml);
            $this->defaultView->render($options);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    abstract protected function validateRequest(): bool;
}
