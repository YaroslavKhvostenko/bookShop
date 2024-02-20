<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin\Head;

use Controllers\AbstractControllers\AbstractAdminController;
use http\Exception\InvalidArgumentException;
use Interfaces\Admin\AdminDataValidatorInterface;
use Models\AbstractProjectModels\Admin\AbstractAdminModel;
use Models\ProjectModels\Admin\AdminModel;
use Views\AbstractViews\AbstractAdminView;
use Views\ProjectViews\Admin\Head\AdminView;
use Models\ProjectModels\Validation\Data\Admin\FactoryValidator;
use mysql_xdevapi\Exception;

class AdminController extends AbstractAdminController
{
    protected AbstractAdminModel $adminModel;
    protected AbstractAdminView $adminView;
    protected ?AdminDataValidatorInterface $dataValidator = null;
    private const ADMINISTRATE = 'administrate';
    private const PROVIDE = 'provide';
    private const REMOVE = 'remove';
    private const REDIRECT = 'redirect';
    private const APPROVE = 'approve';
    private const CANCEL = 'cancel';
    private const HEAD = 'head';
    private const ADMINISTRATE_TITLE = 'Администрирование';
    private const PROVIDE_TITLE = 'Предоставить доступ';
    private const REMOVE_TITLE = 'Снять доступ';
    private const REDIRECT_TITLE = 'Передать должность';
    private const ADMINISTRATE_PAGE = 'administrate.phtml';
    private const ACCESS_PAGE = 'admin_access.phtml';
    private const PAGE_TITLES = [
        self::ADMINISTRATE => self::ADMINISTRATE_TITLE,
        self::PROVIDE => self::PROVIDE_TITLE,
        self::REMOVE => self::REMOVE_TITLE,
        self::REDIRECT => self::REDIRECT_TITLE
    ];
    private const PAGES = [
        self::ADMINISTRATE => self::ADMINISTRATE_PAGE,
        self::PROVIDE => self::ACCESS_PAGE,
        self::REMOVE => self::ACCESS_PAGE,
        self::REDIRECT => self::ACCESS_PAGE,
        self::APPROVE => self::ADMINISTRATE_PAGE,
        self::CANCEL => self::ADMINISTRATE_PAGE,
        self::HEAD => self::ADMINISTRATE_PAGE
    ];

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->adminView = new AdminView();
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function administrateAction(array $params = null)
    {
        try {
            $this->permission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function provideAction(array $params = null): void
    {
        try {
            $this->permission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function removeAction(array $params = null): void
    {
        try {
            $this->permission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function redirectAction(array $params = null): void
    {
        try {
            $this->permission($params);
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    protected function permission(array $params = null): void
    {
        if (!$this->adminModel->getSessModel()->isLogged() || $this->validateRequester()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        $this->getMsgModel(self::REQUEST);
        if (!$this->isNull($params)) {
            $this->wrongData(
                'default',
                'Params have to be empty in ' . $this->getRequestAction() . 'Action!',
                $this->getRequestController(),
                $this->getRequestAction()
            );

            return;
        }

        $this->adminModel->setMsgModel($this->msgModel);
        $data = $this->adminModel->getAdmins($this->getRequestAction());
        $this->adminView->render(
            $this->adminView->getOptions(
            $this->getPageTitle($this->getRequestAction()), $this->getPage($this->getRequestAction()), $data
            )
        );
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function accessAction(array $params = null): void
    {
        try {
            if (!$this->adminModel->getSessModel()->isLogged() || $this->validateRequester()) {
                $this->redirectHomeByCustomerType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();
            }

            $this->getMsgModel(self::REFERER);
            if (!$this->isNull($params)) {
                $this->wrongData(
                    'default',
                    'Params have to be null in ' . $this->getRequestAction() . 'Action!',
                    $this->getRequestController(),
                    self::ADMINISTRATE
                );
            } else {
                $result = $this->getDataValidator(self::REFERER)->emptyCheckBox($this->postInfo->getData());
                if (in_array(false, $result)) {
                    $this->checkResult($result, self::EMPTY, $this->getRefererAction());
                } else {
                    $result = $this->dataValidator->correctCheckBox($result, $this->getRefererAction());
                    $this->adminModel->setMsgModel($this->msgModel);
                    $this->adminModel->changeAccess($result, $this->dataValidator->getFieldName());
                    $this->prepareRedirect(
                        $this->createRedirectString($this->getRefererController(), self::ADMINISTRATE)
                    );
                }
            }
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
    }

    /**
     * @param array $result
     * @param string $messagesType
     * @param $actionType
     * @throws \Exception
     */
    protected function checkResult(array $result, string $messagesType, $actionType): void
    {
        foreach ($result as $field => $value) {
            if (!$value) {
                $this->msgModel->setMsg($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($this->getRefererController(), $actionType));
    }

    protected function prepareRedirect(string $url = null): void
    {
        parent::prepareRedirect('head/'. $url);
    }

    protected function redirectHomeByCustomerType(): void
    {
        if ($this->adminModel->getSessModel()->isAdmin()) {
            parent::prepareRedirect();
        } else {
            $this->redirect();
        }
    }

    protected function validateRequester(): bool
    {
        return !$this->adminModel->getSessModel()->isAdmin() || !$this->adminModel->getSessModel()->isHeadAdmin();
    }

    private function getPageTitle(string $actionName): string
    {
        if (!array_key_exists($actionName, self::PAGE_TITLES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const FIELDS!'
            );
        }

        return self::PAGE_TITLES[$actionName];
    }

    private function getPage(string $actionName): string
    {
        if (!array_key_exists($actionName, self::PAGES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const FIELDS!'
            );
        }

        return self::PAGES[$actionName];
    }

    /**
     * @param string $uriType
     * @return AdminDataValidatorInterface
     * @throws \Exception
     */
    protected function getDataValidator(string $uriType): AdminDataValidatorInterface
    {
        if (!$this->dataValidator) {
            switch (strtolower($uriType)) {
                case 'request' :
                    $this->dataValidator = FactoryValidator::getValidator(
                        $this->getRequestAdminType(), $this->getRequestAction()
                    );
                    break;
                case 'referer' :
                    $this->dataValidator = FactoryValidator::getValidator(
                        $this->getRefererAdminType(), $this->getRefererAction()
                    );
                    break;
                default :
                    throw new \Exception('Wrong URI type declaration for creation of DataValidator');
            }
        }

        return $this->dataValidator;
    }

    protected function redirectHome(): void
    {
        parent::prepareRedirect();
    }
}
