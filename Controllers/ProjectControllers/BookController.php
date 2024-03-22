<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

use Controllers\AbstractControllers\AbstractBookController;
use Models\ProjectModels\BookModel;
use Views\ProjectViews\BookView;
use Models\ProjectModels\Session\User\SessionModel;

class BookController extends AbstractBookController
{
    public function __construct()
    {
        parent::__construct(new BookModel(), new BookView(), SessionModel::getInstance());
    }

    protected function validateRequester(): bool
    {
        return $this->sessionModel->isAdmin();
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect($url);
    }

    protected function redirectHome(): void
    {
        $this->redirect();
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function addAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Property $params have to be not NULL!',
                    'book',
                    'show',
                    $this->serverInfo->getRefererParams()
                );

                return;
            }

            $this->getDataValidator('request')->validateParams($params);
            $result = $this->dataValidator->emptyCheck($this->postInfo->getData());
            if (in_array('', $result)) {
                $this->checkResult($result, 'empty', '');
            } else {
                $result = $this->dataValidator->correctCheck($result);
                if (in_array('', $result, true)) {
                    $this->checkResult($result, 'wrong', '');
                } else {
                    $this->bookModel->setMessageModel($this->msgModel);
                    $this->bookModel->addItem(
                        $result,
                        $this->dataValidator->returnProductOption(),
                        $this->dataValidator->returnProductId(),
                    );
                }
            }

            $this->prepareRedirect(
                $this->createRedirectString('book', 'show', $this->serverInfo->getRefererParams())
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'book', 'show', $this->serverInfo->getRefererParams());
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function updateAction(array $params = null): void
    {
        try {
            if ($this->validateRequester() || !$this->sessionModel->isLoggedIn()) {
                $this->redirectHomeByUserType();

                return;
            }

            if (!$this->getPostInfo()->isPost()) {
                $this->redirectHome();

                return;
            }

            $this->getMsgModel('request');
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Property $params have to be not NULL!',
                    'book',
                    'show',
                    $this->serverInfo->getRefererParams()
                );

                return;
            }

            $this->getDataValidator('request')->validateParams($params);
            $result = $this->dataValidator->emptyCheck($this->postInfo->getData());
            if (in_array('', $result)) {
                $this->checkResult($result, 'empty', '');
            } else {
                $result = $this->dataValidator->correctCheck($result);
                if (in_array('', $result, true)) {
                    $this->checkResult($result, 'wrong', '');
                } else {
                    $this->bookModel->setMessageModel($this->msgModel);
                    $this->bookModel->updateItem(
                        $result,
                        $this->dataValidator->returnProductOption(),
                        $this->dataValidator->returnProductId(),
                    );
                }
            }

            $this->prepareRedirect(
                $this->createRedirectString('book', 'show', $this->serverInfo->getRefererParams())
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'book', 'show', $this->serverInfo->getRefererParams());
        }
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function showAction(array $params = null): void
    {
        try {
            if ($this->validateRequester()) {
                $this->redirectHomeByUserType();

                return;
            }

            $this->getMsgModel();
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Property $params have to be not NULL!',
                    'catalog',
                    'show'
                );

                return;
            }

            $bookId = $this->getDataValidator('request')->validateParams($params);
            $this->bookModel->setMessageModel($this->msgModel);
            $bookDetails = $this->bookModel->getBookDetails($bookId);
            $this->bookView->render(
                $this->bookView->getOptions($bookDetails['book_title'], 'pdp.phtml', $bookDetails)
            );
        } catch (\Exception $exception) {
            $this->catchException($exception, 'catalog', 'show');
        }
    }
}
