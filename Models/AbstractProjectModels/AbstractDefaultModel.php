<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\ProjectModels\Logger;
use Models\ProjectModels\DataRegistry;

abstract class AbstractDefaultModel
{
    protected IDataManagement $sessionInfo;
    protected ?IDataManagement $fileInfo = null;
    public Logger $logger;

    /**
     * AbstractDefaultModel constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->logger = new Logger();
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
    }

    public function isSigned(): bool
    {
        return $this->sessionInfo->isLogged();
    }

    public function isAdmin(): bool
    {
        return isset($this->sessionInfo->getUser()['is_admin']);
    }

    public function moveUploadFile(string $folder, string $fileName = null): string
    {
        $fileName = $fileName ?? rand() . $this->fileInfo->getFileName();
        move_uploaded_file(
            $this->fileInfo->getFileTmpName(),
            $this->fileInfo->getFullImagePath($folder) . $fileName
        );

        return $fileName;
    }

    /**
     * @return IDataManagement
     * @throws \Exception
     */
    protected function getFileInfo(): IDataManagement
    {
        if (!$this->fileInfo) {
            $this->fileInfo = DataRegistry::getInstance()->get('file');
        }

        return $this->fileInfo;
    }
}
