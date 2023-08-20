<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Interfaces\IDataManagement;
use Interfaces\IMySqlInterface;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

class DefaultModel
{
    /**
     * Object for access to session data
     *
     */
    protected IDataManagement $sessionInfo;

    /**
     * Object for access to file data
     *
     */
    protected IDataManagement $fileInfo;

    public Logger $logger;

    protected IMySqlInterface $db;

    /**
     * Set connecting params and connect with database
     *
     * @throws \PDOException
     * @throws \Exception
     */
    public function __construct()
    {
        $this->logger = new Logger();
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->fileInfo = DataRegistry::getInstance()->get('file');
        $this->db = new MySqlDbWorkModel();
    }

    /**
     * Get user
     *
     * @return array|false
     */
    public function isSigned()
    {
//        return $this->sessionInfo->getUser() ?? false;
        return $this->sessionInfo->isLogged();
    }

    public function isAdmin(): bool
    {
        return isset($this->sessionInfo->getUser()['is_admin']);
    }

    /**
     * Move uploaded file from temporary folder to right folder in project
     *
     * @param string $folder
     * @param null|string $fileName
     * @return string
     */
    public function moveUploadFile(string $folder, string $fileName = null): string
    {
        $fileName = $fileName ?? rand() . $this->fileInfo->getFileName();
        move_uploaded_file($this->fileInfo->getFileTmpName(),
            $this->fileInfo->getFullImagePath($folder) . $fileName);
        return $fileName;
    }
}
