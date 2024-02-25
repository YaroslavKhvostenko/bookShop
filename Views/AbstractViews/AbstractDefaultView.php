<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\ProjectModels\Session\Message\SessionModel as MessageSessionModel;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel as UserSessionModel;
use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;

abstract class AbstractDefaultView
{
    protected const LAYOUTS_PATH = 'layouts/';
    protected IDataManagement $serverInfo;
    protected MessageSessionModel $msgSessModel;
    protected UserSessionModel $userSessModel;

    public function __construct(UserSessionModel $userSessModel)
    {
        $this->serverInfo = DataRegistry::getInstance()->get('server');
        $this->msgSessModel = MessageSessionModel::getInstance();
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
        $options['user'] = $this->userSessModel->getCustomerData();
        $options['data'] = $data;
        $options['resultMsg'] = $this->msgSessModel->getMessages();
        return $options;
    }

    protected function getHeaderContent(): string
    {
        $phtml = '.phtml';
        if (!$this->userSessModel->isLoggedIn()) {
            $headerContent = 'header';
        } else {
            if (!$this->userSessModel->isAdmin()) {
                $headerContent = 'user_header';
            } else {
                $headerContent = 'admin_header';
            }
        }

        return $headerContent . $phtml;
    }

    public function getHeader(): string
    {
        return $this->getHeaderPath() . $this->getHeaderContent();
    }

    protected function getHeaderPath(): string
    {
        return $this->getPath();
    }

    protected function getPath(): string
    {
        return self::LAYOUTS_PATH;
    }

    protected function getContentPath(): string
    {
        return $this->getPath();
    }
}
