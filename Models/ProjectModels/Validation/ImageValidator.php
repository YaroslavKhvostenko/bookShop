<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation;

class ImageValidator
{
    private array $fileData;
    private const USERS_IMG_SIZE = 'users';
    private const ADMINS_IMG_SIZE = 'admins';
    private const BOOKS_IMG_SIZE = 'book';
    private array $errors = [];
    private const IMG_MAX_SIZE = [
        self::USERS_IMG_SIZE => 524288,
        self::ADMINS_IMG_SIZE => 524288,
        self::BOOKS_IMG_SIZE => 1048576
    ];

    public function __construct(array $data)
    {
        $this->fileData = $data;
    }

    public function validate(string $type): bool
    {
        if (!$this->checkType()) {
            $this->errors['image_type'] = false;
        }

        if (!$this->checkSize($type)) {
            $this->errors['image_size'] = false;
        }

        return !$this->errors;
    }

    private function checkType(): bool
    {
        return $this->fileData['image']['type'] == 'image/jpeg' || $this->fileData['image']['type'] == 'image/png';
    }

    private function checkSize(string $belongToType): bool
    {
        return $this->fileData['image']['size'] <= self::IMG_MAX_SIZE[$belongToType];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
