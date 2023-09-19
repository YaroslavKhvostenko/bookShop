<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

abstract class AbstractBaseController
{
    protected function redirect(string $url = null): void
    {
        header('Location: /' . $url);
    }

    abstract protected function prepareRedirect(string $url = null): void;
}
