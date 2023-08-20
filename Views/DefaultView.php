<?php
declare(strict_types=1);

namespace Views;

use Models\ProjectModels\DataRegistry;
use Interfaces\IDataManagement;

/**
 * Default class for extends
 *
 * @package View
 */
class DefaultView
{
    protected const LAYOUTS_PATH = 'layouts/';

    protected const ADMIN_LAYOUTS = 'admin/';
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
        $options['content'] = self::LAYOUTS_PATH . $content;
        $options['title'] = $title;
        $options['header'] = self::LAYOUTS_PATH . 'header.phtml';
        $options['messages'] = self::LAYOUTS_PATH . 'messages.phtml';
        $options['footer'] = self::LAYOUTS_PATH . 'footer.phtml';
        $options['user'] = $this->sessionInfo->getUser();
        $options['data'] = $data;
        $options['resultMsg'] = $this->sessionInfo->getAllMessages();
        return $options;
    }

    public function getRefererUri(): string
    {
        return $this->serverInfo->getReferer();
    }

    public function getHeaderContent(): void
    {
        $requestUri = explode('/', trim($this->serverInfo->getRequestUri(), '/'));
        if (ucfirst($requestUri[0]) === 'Admin') {
            if (isset($this->sessionInfo->getUser()['admin'])) {
                include_once self::LAYOUTS_PATH . self::ADMIN_LAYOUTS . 'admin_logged_header.phtml';
            } else {
                include_once self::LAYOUTS_PATH . self::ADMIN_LAYOUTS . 'admin_header.phtml';
            }
        } else {
            if ($this->sessionInfo->getUser()) {
                include_once self::LAYOUTS_PATH . 'user_logged_header.phtml';
            } else {
                include_once self::LAYOUTS_PATH . 'user_header.phtml';
            }
        }
    }
}
