<?php
declare(strict_types=1);

namespace Models\ProjectModels\Sql\MySql;

use Interfaces\IMySqlInterface;
use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Sql\AbstractSqlModel;
use Models\ProjectModels\DataRegistry;

class MySqlDbWorkModel extends AbstractSqlModel implements IMySqlInterface
{
    private IDataManagement $config;
    private \PDO $pdo;
    private static MySqlDbWorkModel $selfInstance;
    private ?string $sql = null;
    private string $methodsPath = 'Class : MySqlDbWorkModel(). Methods : ';
    private $stmt;

    /**
     * Set connecting params and connect with database
     *
     * @throws \PDOException
     * @throws \Exception
     */
    private function __construct()
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

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance(): MySqlDbWorkModel
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new self;
        }

        return self::$selfInstance;
    }

    public function select(array $requestFields): self
    {
        $this->methodsPath = 'select()';
        $this->sql = 'SELECT ';
        $i = 1;
        $count = count($requestFields);
        foreach ($requestFields as $field => $value) {
            if (!is_numeric($field)) {
                if ($i === $count) {
                    $this->sql .= "`{$field}` AS `{$value}`";
                } else {
                    $this->sql .= "`{$field}` AS `{$value}`, ";
                }
            } else {
                if ($i === $count) {
                    $this->sql .= "`{$value}`";
                } else {
                    $this->sql .= "`{$value}`, ";
                }
            }

            $i++;
        }

        return self::$selfInstance;
    }

    public function from(
        array $requestTables,
        array $joinTables = null,
        array $joinConditions = null,
        array $joinTypes = null
    ): self {
        $this->methodsPath .= '->from()';
        $this->sql .= ' FROM ';
        $i = 1;
        $count = count($requestTables);
        foreach ($requestTables as $requestTable) {
            if ($i === $count) {
                $this->sql .= "`{$requestTable}`";
            } else {
                $this->sql .= "`{$requestTable}`, ";
            }

            $i++;
        }

        if ($joinTables !== null) {
            foreach ($joinTables as $joinTable) {
                $joinType = array_shift($joinTypes);
                $requestField = array_key_first($joinConditions);
                $joinField = array_shift($joinConditions);
                $this->sql .= " {$joinType} `{$joinTable}` ON `{$requestField}` = `{$joinField}`";
            }
        }

        return self::$selfInstance;
    }

    /**
     * @param array $conditionData
     * @param array|null $andOr
     * @return $this
     * @throws \Exception
     */
    public function condition(array $conditionData, array $andOr = null): self
    {
        $this->methodsPath .= '->condition()';
        $globalCounter = 1;
        $globalCount = count($conditionData);
        $this->sql .= ' WHERE ';
        foreach ($conditionData as $field => $data) {
            $this->sql .= "`{$field}` ";
            if (is_array($data)) {
                $arrayDataCounter = 1;
                $countDataArray = count($data);
                $this->sql .= "IN (";
                foreach ($data as $value) {
                    $value = $this->handleData($value);
                    if ($arrayDataCounter === $countDataArray) {
                        $this->sql .= "{$value}) ";
                        if (is_array($andOr)) {
                            $conditionOperator = array_shift($andOr);
                            $this->sql .= $conditionOperator . ' ';
                        }
                    } else {
                        $this->sql .= "{$value}, ";
                    }
                    $arrayDataCounter++;
                }
            } else {
                if (!is_null($data)) {
                    $data = $this->handleData($data);
                }  else {
                    $data = 'NULL';
                }

                $operator = $data === 'NULL' ? 'IS' : '=';
                if ($globalCounter === $globalCount) {
                    $this->sql .= "{$operator} {$data}";
                } else {
                    $this->sql .= "{$operator} {$data} ";
                    if (is_array($andOr)) {
                        $conditionOperator = array_shift($andOr);
                        $this->sql .= $conditionOperator . ' ';
                    }
                }
            }

            $globalCounter++;
        }


        return self::$selfInstance;
    }

    /**
     * @param null $data
     * @return false|int|string
     * @throws \Exception
     */
    private function handleData($data)
    {
        $this->methodsPath .= '->handleData()';
        if (is_numeric($data)) {
            $data = (int)$data;
        } elseif (is_string($data)) {
            $data = $this->pdo->quote($data);
        } else {
            throw new \Exception('Wrong data type in ' . "$this->methodsPath!");
        }

        return $data;
    }

    public function orderBy(string $orderField, string $orderType): self
    {
        $this->methodsPath .= '->orderBy()';
        $this->sql .= ' ORDER BY ' . $orderField . ' ' . $orderType;

        return self::$selfInstance;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function query(): self
    {
        $this->methodsPath .= '->query()';
        try {
            $this->stmt = $this->pdo->query($this->sql);
            return self::$selfInstance;
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error selecting data from DB.' . "\n" .
                'Check : ' .$this->methodsPath . ' !!!' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Line: '    . $PDOException->getLine()
            );
        }

        return self::$selfInstance;
    }

    /**
     * @return mixed|\PDOStatement
     * @throws \Exception
     */
    public function fetch()
    {
        $this->methodsPath .= '->fetch()';
        try {
            if ($this->stmt instanceof \PDOStatement) {
                return $this->stmt->fetch();
            }
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error selecting data from DB.' . "\n" .
                'Check : ' .$this->methodsPath . ' !!!' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Line: '    . $PDOException->getLine()
            );
        }

        return $this->stmt;
    }

    /**
     * @return array|\PDOStatement
     * @throws \Exception
     */
    public function fetchAll()
    {
        $this->methodsPath .= '->fetch()';
        try {
            if ($this->stmt instanceof \PDOStatement) {
                return $this->stmt->fetchAll();
            }
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error selecting data from DB.' . "\n" .
                'Check : ' .$this->methodsPath . ' !!!' . "\n" .
                "Error: "      . $PDOException->getMessage() . "\n" .
                'File: '      . $PDOException->getFile() . "\n" .
                'Line: '    . $PDOException->getLine()
            );
        }

        return $this->stmt;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function insertData(string $tableName, array $data): bool
    {
        $insert = 'INSERT INTO ' . "`{$tableName}` (";
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
     * @param array $updateTables
     * @param array $updateData
     * @return $this
     * @throws \Exception
     */
    public function update(array $updateTables, array $updateData): self
    {
        $this->methodsPath .= 'update()';
        $this->sql = 'UPDATE ';
        $i = 1;
        $count = count($updateTables);
        foreach ($updateTables as $updateTable) {
            if ($i === $count) {
                $this->sql .= "`{$updateTable}` ";
            } else {
                $this->sql .= "`{$updateTable}`, ";
            }

            $i++;
        }

        $this->sql .= 'SET ';
        $i = 1;
        $count = count($updateData);
        foreach ($updateData as $key => $value) {
            if (is_numeric($value)) {
                $value = (int)$value;
            } elseif (is_string($value)) {
                $value = $this->pdo->quote($value);
            } elseif (is_null($value)) {
                $value = 'NULL';
            } else {
                throw new \Exception('Wrong data type in ' . $this->methodsPath . ' !');
            }

            if ($i === $count) {
                $this->sql .= "`{$key}` = " . $value;
            } else {
                $this->sql .= "`{$key}` = $value, ";
            }

            $i++;
        }

        return self::$selfInstance;
    }

    /**
     * @return false|int
     * @throws \Exception
     */
    public function exec()
    {
        try{
            return $this->pdo->exec($this->sql);
        } catch (\PDOException $PDOException) {
            $this->catchException(
                $PDOException,
                'Error updating data in DB.' . "\n" .
                'Check : ' . $this->methodsPath . ' !'
            );
        }

        return false;
    }

    public function getLastInsertedId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
