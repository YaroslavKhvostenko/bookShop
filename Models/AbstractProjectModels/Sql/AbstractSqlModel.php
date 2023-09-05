<?php

namespace Models\AbstractProjectModels\Sql;

/**
 * abstract Class AbstractSqlModel
 * @package Models\AbstractProjectModels\Sql
 */
abstract class AbstractSqlModel
{
    abstract public function selectData(string $tableName, array $field, string $condition = null);

    abstract public function insertData(string $tableName, array $data);
}
