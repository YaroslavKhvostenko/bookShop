<?php
declare(strict_types=1);

namespace Models\ProjectModels\Post;

use Interfaces\IDataManagement;

/**
 * @package Models\ProjectModels\Post
 */
class Manager implements IDataManagement
{
    private array $data;

    public function __construct()
    {
        $this->data = $_POST;
    }

    /**
     * Check for valid array POST
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->data);
    }

    /**
     * Get data from POST
     *
     * @return array
     */
    public function getPostData(): array
    {
        return $this->data;
    }
}