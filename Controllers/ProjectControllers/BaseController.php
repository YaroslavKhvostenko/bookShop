<?php
declare(strict_types=1);

namespace Controllers\ProjectControllers;

/**
 * Base controller for extends with base methods
 *
 * @package Controllers\ProjectControllers
 */
class BaseController
{
    /**
     * Set header Location by url
     *
     * @param string $url
     * @return void
     */
    public function location(string $url): void
    {
        header('Location: ' . $url);
    }

    /**
     * Set header Location home
     *
     * @return void
     */
    public function homeLocation(): void
    {
        header('Location: /');
    }

    /**
     * Set header Location by url for admin
     *
     * @param string $url
     * @return void
     */
    public function adminLocation(string $url): void
    {
        header('Location: /admin' . $url);
    }

    /**
     * Set header Location home for admin
     *
     * @return void
     */
    public function adminHomeLocation(): void
    {
        header('Location: /admin');
    }
}
