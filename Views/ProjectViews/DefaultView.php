<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractDefaultView;

/**
 * Class DefaultView
 * @package Views\ProjectViews
 */
class DefaultView extends AbstractDefaultView
{
    protected function getContentPath(): string
    {
        return $this->getPath();
    }
}
