<?php
declare(strict_types=1);

namespace Models\ProjectModels\Sql\MySql;

use Interfaces\IMySqlInterface;
use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Sql\AbstractSqlModel;
use Models\ProjectModels\DataRegistry;

class MySqlDbWorkModel extends AbstractSqlModel implements IMySqlInterface
{
    protected IDataManagement $config;
    private \PDO $pdo;

    /**
     * Set connecting params and connect with database
     *
     * @throws \PDOException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->config = DataRegistry::getInstance()->get('config');
        $db_params = $this->config->getDBdata();
        $options = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
        $dsn = "mysql:host={$db_params['host']};dbname={$db_params['dbname']}; charset={$db_params['charset']}";
        try {
            $this->pdo = new \PDO($dsn, $db_params['user'], $db_params['password'], $options);
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error connection to data base.' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Line: '    . $PDOException->getLine()
            );
        }
    }

    /**
     * Select data from database
     * @param string $tableName
     * @param array $requestFields
     * @param string|null $conditionData
     * @return array|false
     */
    public function selectData(string $tableName, array $requestFields, array $conditionData = null)
    {
        $sql = 'SELECT ';
        $i = 1;
        $count = count($requestFields);
        foreach ($requestFields as $field) {
            if ($i === $count) {
                $sql .= "`{$field}` ";
            } else {
                $sql .= "`{$field}`, ";
            }
            $i++;
        }
        $sql .= "FROM `{$tableName}`";
        if (isset($conditionData)) {
            $i = 1;
            $condition = ' WHERE ';
            foreach ($conditionData as $field => $value) {
                if (!is_string($value) && !is_int($value)) {
                    throw new \Exception('Wrong data type!');
                }

                if ($i === 1) {
                    $condition .= "`{$field}` = " . $this->pdo->quote($value);
                } else {
                    $condition .= " OR `{$field}` = " . $this->pdo->quote($value);
                }
                $i++;
            }
            $sql .= $condition;
        }
        try {
            $result = $this->pdo->query($sql);

            return $result->fetchAll();
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error selecting data from DB.' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Line: '    . $PDOException->getLine()
            );
        }

        return false;
    }

    /**
     * Method for insert data to database
     *
     * @param string $tableName
     * @param array $data
     * @return bool
     * @throws \PDOException
     */
    public function insertData(string $tableName, array $data): bool
    {
        $insert = "INSERT INTO `{$tableName}` (";
        $values = 'VALUES (';
        $i = 1;
        $count = count($data);
        foreach ($data as $field => $value) {
            if ($i == $count) {
                $insert .= "`{$field}`)";
                $values .= ":{$field})";
                break;
            }
            $insert .= "`{$field}`, ";
            $values .= ":{$field}, ";
            $i++;
        }
        $sql = $insert . $values;
        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($data as $field => $value) {
                $stmt->bindValue(":{$field}", $value);
            }

            return $stmt->execute();
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error inserting data to DB.' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Line: '    . $PDOException->getLine()
            );
        }

        return false;
    }

    /**
     * Update data in database
     *
     * @param string $tableName
     * @param array $data
     * @param array $condition
     * @return false|int
     */
    public function updateData(string $tableName, array $updateData, array $conditionData)
    {
        $sql = "UPDATE `{$tableName}` SET ";
        $i = 1;
        $count = count($updateData);
        foreach ($updateData as $key => $value) {
            if ($i === $count) {
                if ($value !== null) {
                    $sql .= "`{$key}`={$this->pdo->quote("$value")} ";
                } else {
                    $sql .= "`{$key}`=NULL ";
                }
//                $sql .= "`{$key}`={$this->pdo->quote("$value")} ";
            } else {
                if ($value !== null) {
                    $sql .= "`{$key}`={$this->pdo->quote("$value")}, ";
                } else {
                    $sql .= "`{$key}`=NULL, ";
                }

//                $sql .= "`{$key}`={$this->pdo->quote("$value")}, "; 1434486929dark tower.jpg
            }
            $i++;
        }
        $i = 1;
        $count = count($conditionData);
        $condition = 'WHERE ';
        foreach ($conditionData as $field => $value) {
            if ($count === $i) {
                $condition .= $field . ' IN ' . '(' . "'{$value}'" . ')';
            }
            $i++;
        }

        $sql .= $condition;
        try {
            return $this->pdo->exec($sql);
        } catch (\PDOException $PDOException) {
            $this->catchException($PDOException, 'Error updating data in DB.');
        }

        return false;
    }

    public function getLastInsertedId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
