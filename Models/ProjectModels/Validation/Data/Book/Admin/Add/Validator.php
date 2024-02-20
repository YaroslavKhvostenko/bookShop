<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Book\Admin\Add;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Validation\Data\Book\Add\AbstractValidator;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

class Validator extends AbstractValidator
{
    private ?MySqlDbWorkModel $db = null;
    private const URI_PARAMS = [
        'genre',
        'author',
        'book'
    ];

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function emptyCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'title' :
                case 'name' :
                case 'author_id' :
                case 'genre_id' :
                case 'pub_date' :
                case 'description' :
                case 'price' :
                case 'quantity' :
                $resultData[$field] = !$value ? '' : $value;
                    break;
                default:
                    throw new InvalidArgumentException(
                        'Wrong field name :' . "'$field'" . ', during emptyCheck method validation!'
                    );
            }
        }

        return $resultData;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function correctCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'title' :
                case 'name' :
                    $resultData[$key] = $this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $value);
                    break;
                case 'description' :
                    $resultData[$key] = $this->pregMatchStrLen('/[А-Яа-я]{50,300}/u', $value);
                    break;
                case 'pub_date' :
                    $resultData[$key] = $this->checkDate($value);
                    break;
                case 'author_id' :
                case 'genre_id' :
                    $result = $this->compareWithDbData($key, $value);
                    if ($result !== '') {
                        $resultData[$key] = (int)$result;
                    } else {
                        $resultData[$key] = $result;
                    }
                    break;
                case 'price' :
                case 'quantity' :
                    $result = $this->isNumeric($value);
                    if ($result === null) {
                        $resultData[$key] = '';
                    } else {
                        $result = $this->isZero($result);
                        if ($result === null) {
                            $resultData[$key . '_zero'] = '';
                        } else {
                            $resultData[$key] = $result;
                        }
                    }
                    break;
                default :
                    throw new InvalidArgumentException(
                        'Wrong field name during correctCheck method validation!'
                    );
            }
        }

        return $this->strToLower($resultData);
    }

    public function validateUriParam(array $uriParams): ?string
    {
        $uriParams = $this->strToLower($uriParams);
        $param = null;
        foreach ($uriParams as $uriParam) {
            if (!in_array($uriParam, self::URI_PARAMS)) {
                throw new InvalidArgumentException(
                    'Wrong uri param during validateUriParam method! Check what comes from the form!'
                );
            }

            $param = $uriParam;
        }

        return $param;
    }

    private function isNumeric(string $data): ?int
    {
        return is_numeric($data) ? (int)$data : null;
    }

    private function isZero(int $data): ?int
    {
        return $data !== 0 ? $data : null;
    }

    private function checkDate(string $dateString): string
    {
        $result = '';
        $arr = explode(".", $dateString);
        if (count($arr) === 3 && checkdate((int) $arr[1], (int) $arr[0], (int) $arr[2])) {
            $result = $dateString;
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @param string $data
     * @return string|null
     * @throws \Exception
     */
    private function compareWithDbData(string $fieldName, string $data): ?string
    {
        $tableNames = [
            'author_id' => 'authors',
            'genre_id' => 'genres'
        ];

        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
        }

        $dbResult = $this->db->select(['id'])->from([$tableNames[$fieldName]])->query()->fetchAll();
        if($dbResult) {
            $result = [];
            foreach ($dbResult as $item) {
                foreach ($item as $data) {
                    $result[] = $data;
                }
            }
            if (in_array($data, $result)) {
                return $data;
            } else {
                return '';
            }
        } else {
            throw new \Exception(
                'Very strange that selectData from db didn\'t find any ' . $tableNames[$fieldName] .
                ', because before add new book, admin have to add ' . $tableNames[$fieldName] . '!'
            );
        }
    }

    private function pregMatchStrLen(string $pattern, string $dataString): string
    {
        preg_match($pattern, $dataString, $matches);
        $result = '';
        if (isset($matches[0])) {
            $result = strlen($dataString) === strlen($matches[0]) ? $matches[0] : $result;
        }

        return $result;
    }

    private function strToLower(array $data): array
    {
        foreach ($data as $field => $value) {
            if (!is_numeric($value) && $value !== '') {
                if (!is_numeric($field)) {
                    $data[strtolower($field)] = strtolower($value);
                } else {
                    $data[$field] = strtolower($value);
                }
            }
        }

        return $data;
    }
}
