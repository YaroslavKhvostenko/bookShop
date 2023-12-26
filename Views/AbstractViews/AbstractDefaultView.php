<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;

abstract class AbstractDefaultView
{
    protected const LAYOUTS_PATH = 'layouts/';
    protected IDataManagement $sessionInfo;
    protected IDataManagement $serverInfo;

    public function __construct()
    {
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->serverInfo = DataRegistry::getInstance()->get('server');
    }

    public function render(array $options): void
    {
        include_once 'Templates/index.phtml';
    }

    public function getOptions(string $title, string $content, array $data = null): array
    {
        $options['content'] = $this->getContentPath() . $content;
        $options['title'] = $title;
        $options['header'] = $this->getPath() . 'header.phtml';
        $options['messages'] = $this->getPath() . 'messages.phtml';
        $options['footer'] = $this->getPath() . 'footer.phtml';
        $options['user'] = $this->sessionInfo->getUser();
        $options['data'] = $data;
        $options['resultMsg'] = $this->sessionInfo->getAllMessages();
        return $options;
    }

    public function getHeaderContent(): void
    {
        if ($this->sessionInfo->getUser()) {
            include_once $this->getContentPath() . 'user_logged_header.phtml';
        } else {
            include_once $this->getContentPath() . 'user_header.phtml';
        }
    }

    protected function getPath(): string
    {
        return self::LAYOUTS_PATH;
    }

    protected function reqCustomer(): string
    {
        return $this->serverInfo->getRequestOption('customer');
    }

    protected function reqController(): string
    {
        return $this->serverInfo->getRequestOption('controller');
    }

    protected function reqAction(): string
    {
        return $this->serverInfo->getRequestOption('action');
    }

    protected function refAction(): string
    {
        return $this->serverInfo->getRefererOption('action');
    }

    abstract protected function getContentPath(): string;
}
