<?php
declare(strict_types=1);

namespace Models\ProjectModels\Get;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private array $data;

    public function __construct()
    {
        $this->data = $_GET;
    }

    public function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getData(): array
    {
        return $this->data;
    }
}
