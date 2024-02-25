<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Admin\HeadAdmin\Redirect;

use Models\AbstractProjectModels\Validation\Data\Admin\HeadAdmin\Redirect\AbstractValidator;

class Validator extends AbstractValidator
{
    private const FIELD_NAMES = [
        'redirect' => 'is_head'
    ];
    private const FIELD_VALUES = [
        'redirect' => '1'
    ];

    public function __construct()
    {
        $this->fieldNames = self::FIELD_NAMES;
        $this->fieldValues = self::FIELD_VALUES;
    }
}
