<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers\Admin;

use Interfaces\IDataManagement;
use Controllers\AbstractControllers\AbstractBookController;
use Models\ProjectModels\Admin\BookModel;
use Views\ProjectViews\Admin\BookView;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\File;
use Models\ProjectModels\Validation\ImageValidator;
use Models\ProjectModels\Session\Admin\SessionModel;

class BookController extends AbstractBookController
{
    protected ?IDataManagement $fileInfo = null;
    protected ?ImageValidator $imageValidator = null;

    public function __construct()
    {
        parent::__construct(new BookModel(), new BookView(), SessionModel::getInstance());
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function addAction(array $params = null): void
    {
        if ($this->validateRequester()) {
            $this->redirectHomeByUserType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have not to be null in ' . $this->serverInfo->getRequestAction() . 'Action!',
                    'catalog',
                    'show'
                );

                return;
            }

            $this->param = $this->getDataValidator(self::REQUEST)->validateUriParam($params);
            $this->bookModel->setMessageModel($this->msgModel);
            $data = $this->bookModel->add($this->param);
            $this->bookView->setParam($this->param);
            $options = $this->bookView->getOptions(
                $this->bookView->getTitle($this->serverInfo->getRequestAction()) ,
                $this->bookView->getPage($this->serverInfo->getRequestAction()),
                $data
            );
            $this->bookView->render($options);
        } catch (\Exception $exception){
            $this->catchException($exception, self::CATALOG);
        }
    }

    /**
     * @param array $params
     * @throws \Exception
     */
    public function newAction(array $params): void
    {
        if ($this->validateRequester()) {
            $this->redirectHomeByUserType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if (is_null($params)) {
                $this->processWrongRequest(
                    'default',
                    'Params have not to be null in ' . $this->serverInfo->getRequestAction() . 'Action!',
                    'catalog',
                    'show'
                );

                return;
            }

            $this->param = $this->getDataValidator(self::REFERER)->validateUriParam($params);
            $data = $this->dataValidator->emptyCheck($this->postInfo->getData());
            if (in_array('', $data)) {
                $this->checkResult(
                    $data, 'empty', '',
                    $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction(), [$this->param]
                );
            } else {
                $data = $this->dataValidator->correctCheck($data);
                if (in_array('', $data)) {
                    $this->checkResult(
                        $data, 'wrong', '',
                        $this->serverInfo->getRefererController(), $this->serverInfo->getRefererAction(), [$this->param]
                    );
                } else {
                    if ($this->param === 'book' && !$this->newBook()) {
                        return;
                    }

                    $this->bookModel->setMessageModel($this->msgModel);
                    $this->bookModel->newItem($data, $this->param);
                    $this->prepareRedirect(
                        $this->createRedirectString(
                            $this->serverInfo->getRefererController(),
                            $this->serverInfo->getRefererAction(),
                            [$this->param]
                        )
                    );
                }
            }
        } catch (\Exception $exception){
            $this->catchException($exception, 'catalog', 'show');
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function newBook(): bool
    {
        if (!$this->getFileInfo()->isFileSent('image')) {
            $this->msgModel->setMessage('empty', 'image', 'image');

            return false;
        }

        if (!$this->getImageValidator()->validate('book')) {
            $this->checkResult(
                $this->imageValidator->getErrors(),
                'wrong',
                false,
                $this->serverInfo->getRefererController(),
                $this->serverInfo->getRefererAction(),
                [$this->param]
            );

            return false;
        }

        return true;
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    protected function getFileInfo(): IDataManagement
    {
        if (!$this->fileInfo) {
            DataRegistry::getInstance()->register('file', new File\Manager());
            $this->fileInfo = DataRegistry::getInstance()->get('file');
        }

        return $this->fileInfo;
    }


    private function getImageValidator(): ImageValidator
    {
        if (!$this->imageValidator) {
            $this->imageValidator = new ImageValidator($this->fileInfo->getFileData());
        }

        return $this->imageValidator;
    }

    protected function prepareRedirect(string $url = null): void
    {
        $this->redirect('admin/' . $url);
    }

    protected function redirectHome(): void
    {
        $this->redirect('admin/');
    }

    protected function validateRequester(): bool
    {
        return (
            $this->sessionModel->isHeadAdmin() ||
            !$this->sessionModel->isAdmin() ||
            !$this->sessionModel->isApproved()
        );
    }
}
