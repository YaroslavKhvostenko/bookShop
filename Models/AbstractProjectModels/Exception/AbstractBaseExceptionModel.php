<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Exception;

use Models\ProjectModels\Logger;

abstract class AbstractBaseExceptionModel
{
    protected ?Logger $logger = null;

    protected function getLogger(): Logger
    {
        if ($this->logger === null) {
            $this->logger = Logger::getInstance();
        }

        return $this->logger;
    }
}
