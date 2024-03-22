<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Task;

use Interfaces\Validator\Task\ValidatorInterface;
use Models\AbstractProjectModels\Validation\Data\AbstractBaseValidator;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

abstract class AbstractValidator extends AbstractBaseValidator implements ValidatorInterface
{
    protected ?MySqlDbWorkModel $db = null;

    protected function getDb(): MySqlDbWorkModel
    {
        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
        }

        return $this->db;
    }
}
