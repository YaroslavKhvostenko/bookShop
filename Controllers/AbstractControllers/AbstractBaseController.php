<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\ProjectModels\Logger;

abstract class AbstractBaseController
{
    protected ?Logger $logger = null;

    protected function getLogger(): Logger
    {
        if (!$this->logger) {
            $this->logger = Logger::getInstance();
        }

        return $this->logger;
    }

    protected function redirect(string $url = null): void
    {
        header('Location: /' . $url);
    }

    abstract protected function prepareRedirect(string $url = null): void;
}
