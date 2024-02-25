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
    private array $splits;
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
        $this->splits = $this->serverInfo->splitString($this->serverInfo->getRequestUri());
        if ($this->serverInfo->isAdminArea($this->splits[0])) {
            $this->setFullControllerName(
                ucfirst($this->splits[0]) . '\\' . ucfirst($this->getControllerName(1))
            );
            $this->setActionName(2);
            $this->formatParamsData(3);
        } else {
            $this->setFullControllerName(ucfirst($this->getControllerName(0)));
            $this->setActionName(1);
            $this->formatParamsData(2);
        }
    }

    private function getControllerName(int $indexNumber): string
    {
        $this->controller = !empty($this->splits[$indexNumber])
            ? ucfirst($this->splits[$indexNumber]) . 'Controller'
            : 'IndexController';

        return $this->controller;
    }

    private function setActionName(int $indexNumber): void
    {
        $this->action = (!isset($this->splits[$indexNumber]) || empty($this->splits[$indexNumber]))
            ? 'indexAction' : $this->splits[$indexNumber] . 'Action';
    }

    private function formatParamsData(int $indexCounter): void
    {
        if (!empty($this->splits[$indexCounter])) {
            for ($i = $indexCounter, $count = count($this->splits); $i < $count; $i++) {
                $this->params[] = $this->splits[$i];
            }
        }
    }

    private function setFullControllerName(string $name): void
    {
        $this->controller = '\Controllers\\ProjectControllers\\' . $name;
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
