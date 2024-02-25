<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\AbstractBookModel;
use Views\AbstractViews\AbstractBookView;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel;

abstract class AbstractBookController extends AbstractController
{
    protected const CONTROLLER_NAME = 'Book_Controller';
    protected const REQUEST = 'request';
    protected const REFERER = 'referer';
    protected const CATALOG = 'catalog';
    protected ?string $param = null;
    protected AbstractBookModel $bookModel;
    protected AbstractBookView $bookView;


    public function __construct(
        AbstractBookModel $bookModel,
        AbstractBookView $bookView,
        AbstractSessionModel $sessionModel
    ) {
        parent::__construct($sessionModel);
        $this->bookModel = $bookModel;
        $this->bookView = $bookView;
    }

    /**
     * @param array $result
     * @param string $messagesType
     * @param $checkType
     * @param string $controller
     * @param string $action
     * @param string|null $param
     * @throws \Exception
     */
    protected function checkResult(
        array $result,
        string $messagesType,
        $checkType,
        string $controller,
        string $action,
        string $param = null
    ): void {
        foreach ($result as $field => $value) {
            if ($value === $checkType) {
                $this->msgModel->setMessage($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($controller, $action, $param));
    }

    abstract protected function addAction(array $params = null): void;
    abstract protected function validateRequest(): bool;
    abstract protected function redirectHome(): void;
    abstract protected function redirectHomeByCustomerType(): void;
}
