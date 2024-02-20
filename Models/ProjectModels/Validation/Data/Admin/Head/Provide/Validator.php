<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Admin\Head\Provide;

use Models\AbstractProjectModels\Validation\Data\Admin\Head\AbstractValidator;

class Validator extends AbstractValidator
{
    private const FIELD_NAMES = [
        'provide' => 'is_approved'
    ];
    private const FIELD_VALUES = [
        'provide' => '1'
    ];

    public function __construct()
    {
        $this->fieldNames = self::FIELD_NAMES;
        $this->fieldValues = self::FIELD_VALUES;
    }
}
