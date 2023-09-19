<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractDefaultView;

class DefaultView extends AbstractDefaultView
{
    protected function getContentPath(): string
    {
        return $this->getPath();
    }
}
