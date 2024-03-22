<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Basket\Remove;

use Models\AbstractProjectModels\Validation\Data\Basket\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
{
    public function validateParams(array $paramsData): ?int
    {
        if (count($paramsData) > 1) {
            throw new \Exception('You received too much data! Check what data comes from URI string!');
        }

        $productId = array_shift($paramsData);
        if (!is_numeric($productId)) {
            throw new \Exception(
                '$productId must be a number in quotes, you received something else!'
            );
        }

        return (int)$productId;
    }
}
