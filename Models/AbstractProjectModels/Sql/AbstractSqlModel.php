<?php

namespace Models\AbstractProjectModels\Sql;

use Models\AbstractProjectModels\Exception\DbModels\AbstractExceptionModel;
use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;

abstract class AbstractSqlModel extends AbstractExceptionModel
{
    protected ?AbstractBaseMsgModel $msgModel = null;

    abstract public function selectData(string $tableName, array $field, array $condition = null);

    abstract public function insertData(string $tableName, array $data);

    public function setMsgModel(AbstractBaseMsgModel $msgModel): void
    {
        if (!$this->msgModel) {
            $this->msgModel = $msgModel;
        }
    }
}
