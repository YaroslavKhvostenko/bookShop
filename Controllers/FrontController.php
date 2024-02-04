<?php
declare(strict_types=1);

namespace Controllers;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\Logger;
use Models\ProjectModels\Server;
use Models\ProjectModels\Session;
use Models\ProjectModels\Config;

class FrontController
{
    protected string $controller;
    protected string $action;
    protected ?array $params = null;
    private ?Logger $logger = null;
    private IDataManagement $serverInfo;

    public function __construct()
    {
        try {
            $this->registerData();
            $this->serverInfo = DataRegistry::getInstance()->get('server');
        } catch (\Exception $exception) {
            $this->catchException($exception);
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
            ->register('config', new Config\Manager);
    }

    public function route(): void
    {
        $this->start();
        try {
            $reflector = new \ReflectionClass($this->controller);
            $controller = $reflector->newInstance();
            $action = $reflector->getMethod($this->action);
            $action->invoke($controller, $this->params);
        } catch (\ReflectionException $reflectionException) {
            $this->catchException($reflectionException, 'Creation error of ReflectionClass or ReflectionMethod.');
            $this->ErrorPage404();
        }
    }

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

    public function getRequest(): string
    {
        return $this->serverInfo->getRequestUri();
    }

    private function getFullControllerName(string $name): string
    {
        return '\Controllers\\ProjectControllers\\' . $name;
    }

    public function ErrorPage404(): void
    {
        http_response_code(404);
        include_once('Templates/layouts/404.phtml');
        die();
    }

    private function catchException(\Exception $exception, string $msg = null): void
    {
        $this->getLogger()->logException($exception, $msg);
    }

    private function getLogger(): Logger
    {
        if (!$this->logger) {
            $this->logger = Logger::getInstance();
        }

        return $this->logger;
    }
}
