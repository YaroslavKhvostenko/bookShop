<?php
declare(strict_types=1);

namespace Controllers;

use Interfaces\IDataManagement;
use Models\ProjectModels\Logger;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Server;
use Models\ProjectModels\Session;
use Models\ProjectModels\Post;
use Models\ProjectModels\File;
use Models\ProjectModels\Config;

/**
 * Main class for routing
 *
 * @package Controllers
 */
class FrontController
{
    /**
     * Given controller
     */
    protected string $controller;
    /**
     * Given action
     */
    protected string $action;
    /**
     * Given params values
     */
    protected ?array $params = null;
    /**
     * Object for access to server data
     */
    private IDataManagement $serverInfo;
    public Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
        try {
            $this->registerData();
            $this->serverInfo = DataRegistry::getInstance()->get('server');
        } catch (\Exception $exception) {
            $this->logger->log('default', $exception->getMessage() . $exception->getTraceAsString());
        }
    }

    /**
     * Register server and session models for encapsulating access
     *
     * @return void
     * @throws \Exception
     */
    private function registerData(): void
    {
        $register = DataRegistry::getInstance();
        $register->register('server', new Server\Manager())
            ->register('session', new Session\Manager())
            ->register('post', new Post\Manager())
            ->register('file', new File\Manager())
            ->register('config', new Config\Manager);
    }

    /**
     * Choose the correct controller and action using reflectionClass
     *
     * @return void
     * @throws \ReflectionException
     */
    public function route(): void
    {
        $this->start();
        try {
            $reflector = new \ReflectionClass($this->controller);
            $controller = $reflector->newInstance();
            $action = $reflector->getMethod($this->action);
            $action->invoke($controller, $this->params);
        } catch (\ReflectionException $reflectionException) {
            $this->logger->log('reflection',
                'Creation error of ReflectionClass or ReflectionMethod.' . "\n" .
                'Ошибка: '      . $reflectionException->getMessage() . "\n" .
                'Файл: '      . $reflectionException->getFile() . "\n" .
                'Строка: '    . $reflectionException->getLine());
            $this->ErrorPage404();
        }
    }

    /**
     * Definition and selecting controller and action
     *
     * @return void
     */
    public function start(): void
    {
        $splits = explode('/', trim($this->getRequest(), '/'));
        if (ucfirst($splits[0]) == 'Admin') {
            $this->controller = !empty($splits[1]) ?
                $this->getFullControllerName('Admin\\' . ucfirst($splits[1]) . 'Controller')
                : $this->getFullControllerName('Admin\\IndexController');
            $this->action = !empty($splits[2]) ? $splits[2] . 'Action' : 'indexAction';
            if (!empty($splits[3])) {
                for ($i = 3, $count = count($splits); $i < $count; $i++) {
                    $this->params[] = $splits[$i];
                }
            }
        } else {
            $this->controller = !empty($splits[0]) ?
                $this->getFullControllerName(ucfirst($splits[0]) . 'Controller')
                : $this->getFullControllerName('IndexController');
            $this->action = !empty($splits[1]) ? $splits[1] . 'Action' : 'indexAction';
            if (!empty($splits[2])) {
                for ($i = 2, $count = count($splits); $i < $count; $i++) {
                    $this->params[] = $splits[$i];
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->serverInfo->getRequestUri();
    }

    /**
     * Return correct name for including file with class
     *
     * @param string $name
     * @return string
     */
    private function getFullControllerName(string $name): string
    {
        return '\Controllers\\ProjectControllers\\' . $name;
    }

    /**
     * Set http response code 404, and include 404 not found page
     *
     * @return void
     */
    public function ErrorPage404(): void
    {
        http_response_code(404);
        include_once('Templates/layouts/404.phtml');
        die();
    }
}
