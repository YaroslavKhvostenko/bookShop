<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\File;
use Models\ProjectModels\Logger;

abstract class AbstractDefaultModel
{
    protected IDataManagement $sessionInfo;
    protected ?IDataManagement $fileInfo = null;
    protected ?AbstractBaseMsgModel $msgModel = null;
    protected ?Logger $logger = null;

    /**
     * AbstractDefaultModel constructor.
     * @throws \Exception
     */
    public function __construct()
    {
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

    /**
     * @param string $fileType
     * @param string|null $fileName
     * @return string
     * @throws \Exception
     */
    public function createUniqueFileName(string $fileType, string $fileName = null): string
    {
        return $this->getFileInfo()->createUniqueFileName($fileType, $fileName);
    }

    /**
     * @param string $fileType
     * @param string $folder
     * @param string $fileName
     * @return bool
     * @throws \Exception
     */
    public function moveUploadFile(string $fileType, string $folder, string $fileName): bool
    {
        return $this->getFileInfo()->moveUploadFile($fileType, $folder, $fileName);
    }

    /**
     * @param string $fileType
     * @param string $folder
     * @param string $fileName
     * @throws \Exception
     */
    protected function deleteFile(string $fileType, string $folder, string $fileName): void
    {
        try {
            switch ($fileType) {
                case 'text_file' :
                case 'image' :
                    $this->getFileInfo()->deleteFile($fileType, $folder, $fileName);
                    break;
                default :
                    break;
            }
        } catch (\Exception $exception) {
            $this->catchException($exception);
        }
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

    public function setMsgModel(AbstractBaseMsgModel $msgModel): void
    {
        if (!$this->msgModel) {
            $this->msgModel = $msgModel;
        }
    }

    protected function catchException(\Exception $exception): void
    {
        $this->getLogger()->logException($exception);
    }

    protected function getLogger(): Logger
    {
        if (!$this->logger) {
            $this->logger = Logger::getInstance();
        }

        return $this->logger;
    }
}
