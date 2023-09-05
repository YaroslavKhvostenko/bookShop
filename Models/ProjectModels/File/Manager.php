<?php
declare(strict_types=1);

namespace Models\ProjectModels\File;

use Interfaces\IDataManagement;

/**
 * @package Models\ProjectModels\File
 */
class Manager implements IDataManagement
{
    private const MEDIA_IMAGES_PATH = 'Media/images/';
    private const USERS_IMAGES = 'users';
    private const ADMIN_USER_IMAGES = 'admins';
    private const BOOKS_LOGOS_IMAGES = 'books_catalog';
    private const USERS_IMG_SIZE = 'users';
    private const ADMINS_IMG_SIZE = 'admins';
    private const BOOKS_IMG_SIZE = 'book';
    private const IMG_MAX_SIZE = [
        self::USERS_IMG_SIZE => 524288,
        self::ADMINS_IMG_SIZE => 524288,
        self::BOOKS_IMG_SIZE => 1048576
    ];
    private const IMAGE_FOLDER = [
        self::USERS_IMAGES => 'users/',
        self::ADMIN_USER_IMAGES => 'admin_users/',
        self::BOOKS_LOGOS_IMAGES => 'books_logos/'
    ];

    private array $data;

    public function __construct()
    {
        $this->data = $_FILES;
    }

    public function isImageSent(): bool
    {
        return $this->data['image']['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->data['image']['name'];
    }

    /**
     * @return string
     */
    public function getFileTmpName(): string
    {
        return $this->data['image']['tmp_name'];
    }

    /**
     * @param string $belongToType
     * @return string
     */
    public function getFullImagePath(string $belongToType): string
    {
        return self::MEDIA_IMAGES_PATH . self::IMAGE_FOLDER[$belongToType];
    }

    public function getFileData(): array
    {
        return $this->data;
    }
}
