<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\AbstractProjectModels\Exception\Models\AbstractExceptionModel;
use Models\ProjectModels\DataRegistry;
use Models\ProjectModels\File;

abstract class AbstractDefaultModel extends AbstractExceptionModel
{
    protected IDataManagement $sessionInfo;
    protected ?IDataManagement $fileInfo = null;
    protected ?AbstractBaseMsgModel $msgModel = null;

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
                case 'image' : $this->getFileInfo()->deleteFile($fileType, $folder, $fileName);
                    break;
                default : throw new \Exception('Undefined fileType \'' . $fileType . '\' during deleting file!');
            }
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception);
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
}
