<?php
declare(strict_types=1);

namespace Controllers\AbstractControllers;

use Models\AbstractProjectModels\Exception\Controllers\AbstractExceptionModel;

abstract class AbstractBaseController extends AbstractExceptionModel
{
    protected function redirect(string $url = null): void
    {
        header('Location: /' . $url);
    }

    abstract protected function prepareRedirect(string $url = null): void;
}
