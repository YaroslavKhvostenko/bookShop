<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Admin\HeadAdmin\Remove;

use Models\AbstractProjectModels\Validation\Data\Admin\HeadAdmin\Remove\AbstractValidator;

class Validator extends AbstractValidator
{
    private const FIELD_NAMES = [
        'remove' => 'is_approved'
    ];
    private const FIELD_VALUES = [
        'remove' => '0'
    ];

    public function __construct()
    {
        $this->fieldNames = self::FIELD_NAMES;
        $this->fieldValues = self::FIELD_VALUES;
    }
}
