<?php
declare(strict_types=1);

namespace Models\ProjectModels\Sql\MySql;

use Interfaces\IMySqlInterface;
use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Sql\AbstractSqlModel;
use Models\ProjectModels\Logger;
use Models\ProjectModels\DataRegistry;

/**
 * Class MySqlDbWorkModel for connect to database and select/insert/update/delete needed data
 * @package Models\ProjectModels\Sql\MySql
 */
class MySqlDbWorkModel extends AbstractSqlModel implements IMySqlInterface
{
    protected IDataManagement $config;

    public Logger $logger;

    private \PDO $pdo;

    /**
     * Set connecting params and connect with database
     *
     * @throws \PDOException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->logger = new Logger();
        $this->config = DataRegistry::getInstance()->get('config');
        $db_params = $this->config->getDBdata();
        $options = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
        $dsn = "mysql:host={$db_params['host']};dbname={$db_params['dbname']}; charset={$db_params['charset']}";
        try {
            $this->pdo = new \PDO($dsn, $db_params['user'], $db_params['password'], $options);
        } catch (\PDOException $PDOException) {
            $this->logger->log('pdo',
                'Error connection to data base.' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Строка: '    . $PDOException->getLine());
        }
    }

    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Select data from database
     * @param string $tableName
     * @param array $field
     * @param string|null $condition
     * @return array|false
     */
    public function selectData(string $tableName, array $field, string $condition = null)
    {
        $sql = 'SELECT ';
        $i = 1;
        $count = count($field);
        foreach ($field as $value) {
            if ($i == $count) {
                $sql .= "`{$value}` ";
            } else {
                $sql .= "`{$value}`, ";
            }
            $i++;
        }
        $sql .= "FROM `{$tableName}`";
        if ($condition) {
            $sql .= " WHERE {$condition}";
        }
        try {
            $result = $this->pdo->query($sql);
            return $result->fetchAll();
        } catch (\PDOException $PDOException) {
            $this->logger->log('pdo', $PDOException->getMessage() . $PDOException->getTraceAsString());
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

        } catch (\PDOException $exception) {
            $this->logger->log('pdo', $exception->getMessage() . $exception->getTraceAsString());
        }

        return false;
    }

    /**
     * Update data in database
     *
     * @param string $tableName
     * @param array $field
     * @param string $condition
     * @return false|int
     */
    public function updateData(string $tableName, array $field, string $condition)
    {
        $sql = "UPDATE `{$tableName}` SET ";
        $i = 1;
        $count = count($field);
        foreach ($field as $key => $value) {
            if ($i == $count) {
                $sql .= "`{$key}`={$this->pdo->quote("$value")} ";
            } else {
                $sql .= "`{$key}`={$this->pdo->quote("$value")}, ";
            }
            $i++;
        }
        $sql .= "WHERE {$condition}";
        try {
            return $this->pdo->exec($sql);
        } catch (\PDOException $PDOException) {
            $this->logger->log('pdo', $PDOException->getMessage() . $PDOException->getTraceAsString());
        }

        return false;
    }

    /**
     * Method for delete data from database without given condition
     *
     * @param string $tableName
     * @param string $id
     * @return false|int
     */
    public function deleteData(string $tableName, string $id)
    {
        $sql = "DELETE FROM `{$tableName}` WHERE `id` IN ($id)";
        try {
            return $this->pdo->exec($sql);
        } catch (\PDOException $PDOException) {
            $this->logger->log('pdo', $PDOException->getMessage() . $PDOException->getTraceAsString());
        }

        return false;
    }
}