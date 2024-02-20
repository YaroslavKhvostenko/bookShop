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

class BookController extends AbstractBookController
{
    protected ?IDataManagement $fileInfo = null;
    protected ?ImageValidator $imageValidator = null;

    public function __construct()
    {
        parent::__construct(new BookModel(), new BookView());
    }

    /**
     * @param array|null $params
     * @throws \Exception
     */
    public function addAction(array $params = null): void
    {
        if ($this->validateRequest()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        try {
            $this->getMsgModel(self::REQUEST);
            if ($this->isNull($params)) {
                $this->wrongData(
                    'default',
                    'Params have not to be null in ' . $this->getRequestAction() . 'Action!',
                    $this->getRequestController(),
                    'catalog',
                    'show'
                );

                return;
            }

            $this->param = $this->getDataValidator(self::REQUEST)->validateUriParam($params);
            $this->bookModel->setMsgModel($this->msgModel);
            $data = $this->bookModel->add($this->param);
            $this->bookView->setParam($this->param);
            $options = $this->bookView->getOptions(
                $this->bookView->getTitle($this->getRequestAction()) ,
                $this->bookView->getPage($this->getRequestAction()),
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
        if ($this->validateRequest()) {
            $this->redirectHomeByCustomerType();

            return;
        }

        if (!$this->getPostInfo()->isPost()) {
            $this->redirectHome();

            return;
        }

        try {
            $this->getMsgModel(self::REFERER);
            if ($this->isNull($params)) {
                $this->wrongData(
                    'default',
                    'Params have not to be null in ' . $this->getRequestAction() . 'Action!',
                    'catalog', 'show'
                );

                return;
            }

            $this->param = $this->getDataValidator(self::REFERER)->validateUriParam($params);
            $data = $this->dataValidator->emptyCheck($this->postInfo->getData());
            if (in_array('', $data)) {
                $this->checkResult(
                    $data, 'empty', '',
                    $this->getRefererController(), $this->getRefererAction(), $this->param
                );
            } else {
                $data = $this->dataValidator->correctCheck($data);
                if (in_array('', $data)) {
                    $this->checkResult(
                        $data, 'wrong', '',
                        $this->getRefererController(), $this->getRefererAction(), $this->param
                    );
                } else {
                    if ($this->param === 'book' && !$this->newBook()) {
                        return;
                    }

                    $this->bookModel->setMsgModel($this->msgModel);
                    $this->bookModel->newItem($data, $this->param);
                    $this->prepareRedirect(
                        $this->createRedirectString(
                            $this->getRefererController(), $this->getRefererAction(), $this->param
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
            $this->msgModel->setMsg('empty', 'image', 'image');

            return false;
        }

        if (!$this->getImageValidator()->validate('book')) {
            $this->checkResult(
                $this->imageValidator->getErrors(),
                'wrong',
                false,
                $this->getRefererController(),
                $this->getRefererAction(),
                $this->param
            );

            return false;
        }

        return true;
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    private function getFileInfo(): IDataManagement
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
        $this->prepareRedirect();
    }

    protected function redirectHomeByCustomerType(): void
    {
        if ($this->bookModel->getSessModel()->isHeadAdmin()) {
            $this->prepareRedirect();
        } else {
            $this->redirect();
        }
    }

    protected function isNull($data): bool
    {
        return $data === null;
    }

    protected function validateRequest(): bool
    {
        return $this->bookModel->getSessModel()->isHeadAdmin() ||
            !$this->bookModel->getSessModel()->isAdmin() ||
            !$this->bookModel->getSessModel()->isApproved();
    }

}
