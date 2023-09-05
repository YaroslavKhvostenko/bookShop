<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\ProjectModels\Logger;
use Models\ProjectModels\DataRegistry;

abstract class AbstractDefaultModel
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

    public function __construct()
    {
        $this->logger = new Logger();
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
        $this->fileInfo = DataRegistry::getInstance()->get('file');
    }

    public function isSigned(): bool
    {
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