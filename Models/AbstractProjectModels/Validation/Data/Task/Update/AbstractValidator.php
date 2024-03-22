<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Task\Update;

use Models\AbstractProjectModels\Validation\Data\Task\AbstractValidator as BaseValidator;
use mysql_xdevapi\Exception;

abstract class AbstractValidator extends BaseValidator
{
    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function checkData(array $data): ?array
    {
        $result = $this->emptyCheck($data);
        if (!in_array('', $result)) {
            $result = $this->correctCheck($result);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function emptyCheck(array $data): ?array
    {
        $result = [];
        $emptyResult = $this->emptyValidation($data);
        if (is_null($emptyResult)) {
            $result[$this->getEmptyDataField()] = '';
        } else {
            $result = $this->validateHtmlFormData($emptyResult);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function validateHtmlFormData(array $data): ?array
    {
        $result = [];
        $count = count($data);
        if ($count === 1) {
            foreach ($data as $condition => $update) {
                $result['condition_data'] = $this->splitString($condition);
                $result['update_data'] = $this->splitString($update);
            }
        } else {
            foreach ($data as $condition => $update) {
                $result['condition_data'][] = $this->splitString($condition);
                $result['update_data'][] = $this->splitString($update);
            }

            foreach ($result as $field => $value) {
                $result[$field] = $this->formatMultiLevelArray($value);
            }
        }

        return $result;
    }

    protected function formatMultiLevelArray(array $data): ?array
    {
        $result = [];
        foreach ($data as $field) {
            foreach ($field as $fieldName => $fieldValue) {
                $result[$fieldName][] = $fieldValue;
            }
        }

        $fieldName = array_key_first($result);
        if ($fieldName === $this->getNotUniqueFieldName()) {
            $arrayUnique = array_unique($result[$fieldName]);
            if (count($arrayUnique) === 1) {
                $result[$fieldName] = $arrayUnique[0];
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function correctCheck(array $data): ?array
    {
        $result = [];
        foreach ($data as $field => $array) {
            foreach ($array as $fieldName => $value) {
                $this->validateFieldNames($fieldName);
                if (is_array($value)) {
                    foreach ($value as $valueData) {
                        $result[$field][$fieldName][] = $this->validateFieldValues($valueData, $fieldName);
                    }
                } else {
                    $result[$field][$fieldName] = $this->validateFieldValues($value, $fieldName);
                }
            }
        }

        $this->checkDataWithDb($result);

        return $result;
    }

    /**
     * @param string $fieldName
     * @throws \Exception
     */
    protected function validateFieldNames(string $fieldName): void
    {
        switch ($fieldName) {
            case 'task_id' :
                break;
            default :
                throw new \Exception(
                    'You wrote wrong fieldName :' . '\'' . $fieldName . '\'' .
                    ', in html form, or you forgot to add this fieldName into switch() function!'
                );
        }
    }

    /**
     * @param string $fieldValue
     * @param string|null $fieldName
     * @return int|null
     * @throws \Exception
     */
    protected function validateFieldValues(string $fieldValue, string $fieldName = null): ?int
    {
        if (!is_numeric($fieldValue)) {
            throw new \Exception(
                'Wrong data from \'html select form\'! 
                It mast be a number in quotes like \'1\', probably you sent : ' . "'$fieldValue'"
            );
        }

        return (int)$fieldValue;
    }

    /**
     * @param string $data
     * @return array|null
     * @throws \Exception
     */
    protected function splitString(string $data): ?array
    {
        $splits = array_pad(explode('/', trim($data, '/')), 2, '');
        $countSplits = count($splits);
        if ($countSplits > 2) {
            throw new \Exception(
                'You wrote extra data to \'html form\' value string! Check \'html form\'!'
            );
        }

        $result = [
            $splits[0] ?? '' => $splits[1]
        ];

        if (array_key_first($result) === '' || in_array('', $result)) {
            throw new \Exception(
                'You missed some data from \'html form\' value string! Check \'html form\' form!'
            );
        }

        return $result;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    protected function checkDataWithDb(array $data): void
    {
        foreach ($data as $array) {
            $arrayKeyFirst = array_key_first($array);
            $count = is_array($array[$arrayKeyFirst]) ? count($array[$arrayKeyFirst]) : count($array);
            if ($this->validateSpecificField($arrayKeyFirst)) {
                if ($this->validateChangingField($arrayKeyFirst)) {
                    $changedField = $this->getChangingField($arrayKeyFirst);
                    $condition[$changedField] = is_array($array[$arrayKeyFirst]) ?
                        array_unique($array[$arrayKeyFirst]) :
                        $array[$arrayKeyFirst];

                    $count = count($condition[$changedField]);
                } else {
                    $condition = $array;
                }

                $dbResult = $this->getDb()->
                select($this->getFieldForSelect($arrayKeyFirst))->
                from($this->getDbTable($arrayKeyFirst))->
                condition($condition)->
                query()->
                fetchAll();

                if (!$dbResult || count($dbResult) !== $count) {
                    throw new \Exception(
                        'Very strange that sql request didn\'t find : ' .
                        '\'' . $this->getFieldForSelect($arrayKeyFirst)[0] . '\'' .
                        ' from table :' . '\'' . $this->getDbTable($arrayKeyFirst)[0] . '\'' .
                        ', because it was taken from DB to fill the form!'
                    );
                }

                unset($condition);
            }
        }
    }

    protected function validateSpecificField(string $fieldName): bool
    {
        switch ($fieldName) {
            case 'task_id' :
                return true;
            default :
                return false;
        }
    }

    protected function validateChangingField(string $fieldName = null): bool
    {
        return false;
    }

    /**
     * @param string $fieldName
     * @return string[]|null
     * @throws \Exception
     */
    protected function getFieldForSelect(string $fieldName): ?array
    {
        switch ($fieldName) {
            case 'task_id' :
                return ['task_id'];
            default :
                throw new \Exception('Unknown field name : ' . '\'' . $fieldName . '\'' .
                    ' during selecting field for select() method!' .
                    ' Probably comes wrong fieldName or you forgot to add it into switch() function!');
        }
    }

    /**
     * @param string $fieldName
     * @return string[]|null
     * @throws \Exception
     */
    protected function getDbTable(string $fieldName): ?array
    {
        switch ($fieldName) {
            case 'task_id' :
                return ['tasks'];
            default :
                throw new \Exception('Unknown field name : ' . '\'' . $fieldName . '\'' .
                    ' during selecting table name for select() method!' .
                    ' Probably comes wrong fieldName or you forgot to add it into switch() function!');
        }
    }

    abstract protected function emptyValidation(array $data): ?array;
    abstract protected function getEmptyDataField(): string;
    abstract protected function getNotUniqueFieldName(): string;
}
