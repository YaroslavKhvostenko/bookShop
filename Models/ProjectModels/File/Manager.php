<?php
declare(strict_types=1);

namespace Models\ProjectModels\File;

use Interfaces\IDataManagement;
use Models\AbstractProjectModels\Exception\Models\AbstractExceptionModel;

class Manager extends AbstractExceptionModel implements IDataManagement
{
    private const MEDIA_CONTENT_PATH = 'Media/';
    private const MEDIA_IMAGES_PATH = 'images/';
    private const MEDIA_TEXT_FILES_PATH = 'text_files/';
    private const IMAGE_TYPE = 'image';
    private const TEXT_FILE_TYPE = 'text_file';
    private const USER = 'user';
    private const ADMIN = 'admin';
    private const BOOK = 'book';
    private array $data;
    private const FILE_FOLDER = [
        self::USER => 'users/',
        self::ADMIN => 'admin_users/',
        self::BOOK => 'books_logos/'
    ];
    private const MEDIA_CONTENT = [
        self::IMAGE_TYPE => self::MEDIA_IMAGES_PATH,
        self::TEXT_FILE_TYPE => self::MEDIA_TEXT_FILES_PATH
    ];

    public function __construct()
    {
        $this->data = $_FILES;
    }

    public function isFileSent(string $fileType): bool
    {
        return $this->data[$fileType]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function getFileName(string $fileType): string
    {
        return $this->data[$fileType]['name'];
    }

    public function getFileTmpName(string $fileType): string
    {
        return $this->data[$fileType]['tmp_name'];
    }

    public static function getFullFilePath(string $fileType, string $folderType): string
    {
        return self::MEDIA_CONTENT_PATH . self::MEDIA_CONTENT[$fileType] . self::FILE_FOLDER[$folderType];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFileData(): array
    {
        if (empty($this->data)) {
            throw new \Exception('Проблема при загрузке глобального массива $_FILES');
        }
        return $this->data;
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
        try {
            if (!move_uploaded_file($this->getFileTmpName($fileType),
                self::getFullFilePath($fileType, $folder) . $fileName)
            ) {
                throw new \Exception('Проблема с загрузкой файла на сервер. Проверьте путь или название файла!');
            }

            return true;
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception);
        }

        return false;
    }

    public function createUniqueFileName(string $fileType, string $fileName = null): string
    {
        return $fileName ?? rand() . $this->getFileName($fileType);
    }

    /**
     * @param string $fileType
     * @param string $folder
     * @param string $fileName
     * @throws \Exception
     */
    public function deleteFile(string $fileType, string $folder, string $fileName): void
    {
        try {
            if (!unlink(self::getFullFilePath($fileType, $folder) . $fileName)) {
                throw new \Exception(
                    'Something happened. You have to delete image with your arms by address: ' .
                    self::getFullFilePath($fileType, $folder) . $fileName
                );
            }
        } catch (\Exception $exception) {
            $this->exceptionCatcher($exception);
        }
    }
}
