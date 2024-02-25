<?php
declare(strict_types=1);

namespace Models\ProjectModels\Admin;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\AbstractBookModel;
use Models\ProjectModels\Session\Admin\SessionModel;
use Interfaces\IDataManagement;

class BookModel extends AbstractBookModel
{
    protected ?IDataManagement $fileInfo = null;
    protected ?array $item = null;
    private const ADD_METHOD = 'add';
    private const TABLE_NAMES = [
        'genre' => 'genres',
        'author' => 'authors',
        'book' => 'books_catalog'
    ];
    private const REQUEST_FIELDS = [
        self::ADD_METHOD => self::ADD_METHOD_REQUEST_FIELDS
    ];
    private const ADD_METHOD_REQUEST_FIELDS = [
        'genres' => ['title'],
        'authors' => ['name'],
        'books_catalog' => ['title']
    ];

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    /**
     * @param string $param
     * @return array|null
     * @throws \Exception
     */
    public function add(string $param): ?array
    {
        $data = [];
        $this->setDbMsgModel();
        if ($param === 'book') {
            $dbResult = $this->db->select(['id','title'])->from(['genres'])->query()->fetchAll();
            if ($dbResult) {
                $data['genres'] = $dbResult;
                $result['empty_genres'] = false;
            } else {
                $result['empty_genres'] = true;
            }

            $dbResult = $this->db->select(['id','name'])->from(['authors'])->query()->fetchAll();
            if ($dbResult) {
                $data['authors'] = $dbResult;
                $result['empty_authors'] = false;
            } else {
                $result['empty_authors'] = true;
            }

            if (in_array(true, $result)) {
                $this->checkResult($result, 'empty', true);
            }
        } else {
            $tableName = $this->getTableName($param);
            $requestFields = $this->getRequestFields(self::ADD_METHOD, $tableName);
            $dbResult = $this->db->select($requestFields)->from([$tableName])->query()->fetchAll();
            if (!$dbResult) {
                $this->msgModel->setMessage('empty', 'no_' . $tableName, $tableName);
            }

            $data = $dbResult;
        }

        return $data;
    }

    /**
     * @param array $data
     * @param string $param
     * @throws \Exception
     */
    public function newItem(array $data, string $param): void
    {
        if ($param === 'book') {
            $this->newBook($data, $param);
        } else {
            $this->newBookProperty($data, $param);
        }
    }

    /**
     * @param array $data
     * @param string $param
     * @throws \Exception
     */
    private function newBookProperty(array $data, string $param): void
    {
        $this->new($data, $param);
    }

    /**
     * @param array $data
     * @param string $param
     * @throws \Exception
     */
    private function new(array $data, string $param): bool
    {
        if ($param === 'book') {
            $conditionData['title'] = $data['title'];
        } else {
            $conditionData = $data;
        }

        $this->setDbMsgModel();
        $dbResult = $this->db->select(['id'])->from([$this->getTableName($param)])
            ->condition($conditionData)->query()->fetchAll();
        if ($dbResult) {
            $this->msgModel->setMessage('failure', $param . '_exist', $param . '_exist');

            return false;
        }

        if (!$this->db->insertData($this->getTableName($param), $data)) {
            throw new \Exception(
                'Failure to add new ' . $param . ', check what comes in sql string!'
            );
        } else {
            $this->msgModel->setMessage('success', $param, $param);
        }

        return true;
    }

    /**
     * @param array $data
     * @param string $param
     * @throws \Exception
     */
    private function newBook(array $data, string $param): void
    {
        $data['image'] = $this->createUniqueFileName('image');
        if (!$this->moveUploadFile('image', 'book', $data['image'])) {
            $this->msgModel->setErrorMsg('file');

            return;
        }

        if (!$this->new($data, $param)) {
            $this->deleteFile('image', 'book', $data['image']);
        }
    }

    protected function getTableName(string $param): ?string
    {
        if (!array_key_exists($param, self::TABLE_NAMES)) {
            throw new InvalidArgumentException(
                'Wrong param : ' . "'$param'" . ', during choosing DB table name from const TABLE_NAMES!
                Check this const or what comes here!'
            );
        }

        return self::TABLE_NAMES[$param];
    }

    protected function getRequestFields(string $methodName, string $tableName): ?array
    {
        if (!array_key_exists($methodName, self::REQUEST_FIELDS)) {
            throw new InvalidArgumentException(
                "Invalid method name: '$methodName'."
            );
        }

        if (!array_key_exists($tableName, self::REQUEST_FIELDS[$methodName])) {
            throw new InvalidArgumentException(
                "Invalid table name: '$tableName' for method: '$methodName'."
            );
        }

        return self::REQUEST_FIELDS[$methodName][$tableName];
    }

    /**
     * @param array $result
     * @param string $messagesType
     * @param bool $boolType
     * @throws \Exception
     */
    protected function checkResult(array $result, string $messagesType, bool $boolType): void
    {
        foreach ($result as $field => $value) {
            if ($value == $boolType) {
                $this->msgModel->setMessage($messagesType, $field, $field);
            }
        }
    }
}
