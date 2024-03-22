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
     * @param string|null $controller
     * @param string|null $action
     * @param array|null $params
     * @throws \Exception
     */
    protected function checkResult(
        array $result,
        string $messagesType,
        $checkType,
        string $controller = null,
        string $action = null,
        array $params = null
    ): void {
        foreach ($result as $field => $value) {
            if ($value === $checkType) {
                $this->msgModel->setMessage($messagesType, $field, $field);
            }
        }

        $this->prepareRedirect($this->createRedirectString($controller, $action, $params));
    }

    abstract protected function addAction(array $params = null): void;
}
