<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\AbstractProjectModels\Session\Message\AbstractSessionModel as MsgSessModel;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel as CustomerSessModel;
use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;

abstract class AbstractDefaultView
{
    protected const LAYOUTS_PATH = 'layouts/';
    protected IDataManagement $sessionInfo;
    protected IDataManagement $serverInfo;
    protected MsgSessModel $msgSessModel;
    protected CustomerSessModel $userSessModel;

    public function __construct(MsgSessModel $msgSessModel, CustomerSessModel $userSessModel)
    {
//        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->serverInfo = DataRegistry::getInstance()->get('server');
        $this->msgSessModel = $msgSessModel;
        $this->userSessModel = $userSessModel;
    }

    public function render(array $options): void
    {
        include_once 'Templates/index.phtml';
    }

    public function getOptions(string $title, string $content, array $data = null): array
    {
        $options['content'] = $this->getContentPath() . $content;
        $options['title'] = $title;
        $options['header'] = $this->getPath() . 'factory_header.phtml';
        $options['messages'] = $this->getPath() . 'messages.phtml';
        $options['footer'] = $this->getPath() . 'footer.phtml';
        $options['user'] = $this->userSessModel->getUserData();
        $options['data'] = $data;
        $options['resultMsg'] = $this->msgSessModel->getMessages();
        return $options;
    }

    public function getHeaderContent(): void
    {
        if ($this->userSessModel->getUserData()) {
            if ($this->userSessModel->isAdmin()) {
                if ($this->userSessModel->isHeadAdmin()) {
                    include_once $this->getHeaderPath() . 'admin_header.phtml';
                } else {
                    include_once $this->getHeaderPath() . 'admin_header.phtml';
                }
            } else {
                include_once $this->getHeaderPath() . 'user_header.phtml';
            }
        } else {
            include_once $this->getHeaderPath() . 'header.phtml';
        }
    }

    protected function getHeaderPath(): string
    {
        return $this->getPath();
    }


    protected function getPath(): string
    {
        return self::LAYOUTS_PATH;
    }

    protected function getRequestUserType(): string
    {
        return $this->serverInfo->getRequestOption('user_type');
    }

    protected function getRequestController(): string
    {
        return $this->serverInfo->getRequestOption('controller');
    }

    protected function getRequestAction(): string
    {
        return $this->serverInfo->getRequestOption('action');
    }

    protected function getRefererAction(): string
    {
        return $this->serverInfo->getRefererOption('action');
    }

    abstract protected function getContentPath(): string;
}
