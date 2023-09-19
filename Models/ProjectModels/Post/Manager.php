<?php
declare(strict_types=1);

namespace Models\ProjectModels\Post;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private array $data;

    public function __construct()
    {
        $this->data = $_POST;
    }

    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->data);
    }

    public function getPostData(): array
    {
        return $this->data;
    }
}
