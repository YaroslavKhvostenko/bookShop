<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin\Head;

use Controllers\AbstractControllers\AbstractCatalogController;
use Models\ProjectModels\Admin\Head\CatalogModel;
use Views\ProjectViews\Admin\Head\CatalogView;
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
        return (
            !$this->catalogModel->getSessModel()->isLogged() ||
            !$this->catalogModel->getSessModel()->isAdmin() ||
            !$this->catalogModel->getSessModel()->isHeadAdmin()
        );
    }

    protected function prepareRedirect(string $url = null): void
    {
        if ($this->catalogModel->getSessModel()->isAdmin()) {
            $this->redirect('admin/');
        } else {
            $this->redirect();
        }
    }

    /**
     * @return AbstractBaseMsgModel
     * @throws \Exception
     */
    protected function getMsgModelByReferer(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->msgModel = MsgModelsFactory::getMsgModel($this->getRefererAdminType() , $this->getRefererAction());
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
            $this->msgModel = MsgModelsFactory::getMsgModel($this->getRequestAdminType(), $this->getRequestAction());
        }

        return $this->msgModel;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRequestAdminType(): string
    {
        return $this->getRequestOption('admin_type');
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getRefererAdminType(): string
    {
        return $this->getRefererOption('admin_type');
    }
}
