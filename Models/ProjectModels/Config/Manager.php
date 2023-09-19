<?php
declare(strict_types=1);

namespace Models\ProjectModels\Config;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private ?array $data = null;

    public function __construct()
    {
        if (is_file('data/bookshop_config.php')) {
            $this->data = require_once 'data/bookshop_config.php';
        }
    }

    /**
     * Get database connection data
     *
     * @return array
     * @throws \Exception
     */
    public function getDBdata(): array
    {
        if (!isset($this->data['db_params'])) {
            throw new \PDOException('Params for connecting to database does not exist');
        }

        return $this->data['db_params'];
    }

    public function getAdminPass(): string
    {
        if (!isset($this->data['admin_pass'])) {
            throw new \PDOException('Admin password does not exist!');
        }

        return $this->data['admin_pass'];
    }
}
