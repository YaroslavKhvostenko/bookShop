<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

abstract class AbstractBaseController
{
    /**
     * @param string|null $url
     */
    protected function location(string $url = null): void
    {
        header('Location: /' . $url);
    }

    abstract protected function redirectLocation(string $url = null): void;
}
