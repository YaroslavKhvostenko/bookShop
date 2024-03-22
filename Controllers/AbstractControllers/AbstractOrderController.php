<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\Session\User\AbstractSessionModel;
use Models\AbstractProjectModels\AbstractOrderModel;
use Views\AbstractViews\AbstractOrderView;

abstract class AbstractOrderController extends AbstractController
{
    protected const CONTROLLER_NAME = 'Order_Controller';
    protected AbstractOrderModel $orderModel;
    protected AbstractOrderView $orderView;

    public function __construct(
        AbstractSessionModel $sessionModel,
        AbstractOrderModel $orderModel,
        AbstractOrderView $orderView
    ) {
        parent::__construct($sessionModel);
        $this->orderModel = $orderModel;
        $this->orderView = $orderView;
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }
}
