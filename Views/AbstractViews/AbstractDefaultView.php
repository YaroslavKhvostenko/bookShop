<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;

/**
 * Default class for extends
 *
 * @package View
 */
abstract class AbstractDefaultView
{
    protected const LAYOUTS_PATH = 'layouts/';
    /**
     * Object for access to session data
     */
    protected IDataManagement $sessionInfo;
    /**
     * Object for access to server data
     */
    protected IDataManagement $serverInfo;

    public function __construct()
    {
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->serverInfo = DataRegistry::getInstance()->get('server');
    }

    /**
     * Maim method for rendering data
     *
     * @param array $options
     * @return void
     */
    public function render(array $options): void
    {
        include_once 'Templates/index.phtml';
    }

    /**
     * Set default options for rendering if it's needed
     *
     * @param string $title
     * @param string $content
     * @param array|null $data
     * @return array
     */
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

    abstract protected function getContentPath(): string;
}
