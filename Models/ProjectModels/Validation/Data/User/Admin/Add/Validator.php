<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\Admin\Add;

use Models\AbstractProjectModels\Validation\Data\User\Add\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * ВРЕМЕННАЯ ЗАТЫЧКА
     * @param array $data
     * @return array
     */
    public function emptyCheck(array $data): array
    {
        return $data;
    }

    /**
     * ВРЕМЕННАЯ ЗАТЫЧКА
     * @param array $data
     * @return array
     */
    public function correctCheck(array $data): array
    {
        return $data;
    }
}
