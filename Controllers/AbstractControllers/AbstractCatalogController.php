<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\AbstractCatalogModel;
use Views\AbstractViews\AbstractCatalogView;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;

abstract class AbstractCatalogController extends AbstractController
{
    protected const CONTROLLER_NAME = 'Catalog_Controller';
    protected AbstractCatalogModel $catalogModel;
    protected AbstractCatalogView $catalogView;

    public function __construct(
        AbstractCatalogModel $catalogModel,
        AbstractCatalogView $catalogView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($sessionModel);
        $this->catalogModel = $catalogModel;
        $this->catalogView = $catalogView;
    }

    /**
     * @throws \Exception
     */
    public function showAction(): void
    {
        try {
            if ($this->validateRequest()) {
                $this->prepareRedirect();
            } else {
                $this->catalogModel->setMessageModel($this->getMsgModel('request'));
                $data = $this->catalogModel->getCatalog();
                $options = $this->catalogView->getOptions('Каталог', 'catalog.phtml', $data);
                $this->catalogView->render($options);
            }
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    abstract protected function validateRequest(): bool;
}
