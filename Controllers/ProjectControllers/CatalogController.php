<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractCatalogController;
use Models\ProjectModels\CatalogModel;
use Views\ProjectViews\CatalogView;
use Models\AbstractProjectModels\Message\Catalog\AbstractBaseMsgModel;
use Models\ProjectModels\Message\Catalog\MsgModelsFactory;

class CatalogController extends AbstractCatalogController
{

    public function __construct()
    {
        parent::__construct(new CatalogModel(), new CatalogView());
    }

    protected function validateRequest(): bool
    {
        return $this->catalogModel->getSessModel()->isAdmin();
    }

    protected function prepareRedirect(string $url = null): void
    {
        if ($this->catalogModel->getSessModel()->isAdmin()) {
            $this->redirect('admin/');
        } else {
            $this->redirect();
        }
    }

    protected function getMsgModelByReferer(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MsgModelsFactory::getMsgModel($this->getCustomerType(), $this->getRefererAction());
        }

        return $this->msgModel;
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByRequest(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MsgModelsFactory::getMsgModel($this->getCustomerType(), $this->getRequestAction());
        }

        return $this->msgModel;
    }

    private function getCustomerType(): string
    {
        if ($this->catalogModel->getSessModel()->isLogged()) {
            return 'user';
        }

        return 'not_logged';
    }
}
