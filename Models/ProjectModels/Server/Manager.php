<?php
declare(strict_types=1);

namespace Models\ProjectModels\Server;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private array $data;

    public function __construct()
    {
        $this->data = $_SERVER;
    }

    public function getRequestUri(): string
    {
        return $this->data['REQUEST_URI'];
    }

    public function getReferer(): string
    {
        return $this->data['HTTP_REFERER'];
    }
}
