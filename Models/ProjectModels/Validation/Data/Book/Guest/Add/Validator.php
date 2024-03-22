<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Book\Guest\Add;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Validation\Data\Book\Add\AbstractValidator;
use Models\ProjectModels\Sql\MySql\MySqlDbWorkModel;

class Validator extends AbstractValidator
{
    protected ?int $productId = null;
    protected ?string $productOption = null;
    protected ?MySqlDbWorkModel $db = null;

    public function validateParams(array $uriParams): void
    {
        if (count($uriParams) !== 2) {
            throw new \Exception(
                'Too much or too less data from uri string! You have to receive array with 2 elements'
            );
        }

        $productOption = strtolower($uriParams[0]) ?? '';
        if ($productOption === '' || !$this->checkProductOption($productOption)) {
            throw new \Exception(
                'Unknown product option! Check what comes from uri string!'
            );
        }

        $itemId = $uriParams[1] ?? '';
        if (!is_numeric($itemId)) {
            throw new \Exception(
                'Data from uri string must be numeric, probably you sent a word or empty space!'
            );
        }

        $this->productOption = $productOption;
        $this->productId = (int)$itemId;
        $this->checkProductInDb();
    }

    public function emptyCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch (strtolower($field)) {
                case 'rating' :
                case 'comment' :
                case 'author_name' :
                    $resultData[strtolower($field)] = $value ?? '';
                    break;
                default :
                    throw new \Exception(
                        'Unknown field name in provided data array'
                    );
            }
        }

        return $resultData;
    }

    protected function checkProductOption(string $productDetailSpace): ?bool
    {
        switch (strtolower($productDetailSpace)) {
            case 'rating' :
            case 'comment' :
                return true;
            default :
                return false;
        }
    }

    public function correctCheck(array $data): array
    {
        if (!array_key_exists($this->productOption, $data)) {
            throw new \InvalidArgumentException(
                'Value of variable $this->productOption didn\'t find in provided data array!'
            );
        }

        $resultData = [];
        foreach ($data as $field => $value) {
            switch (strtolower($field)) {
                case 'rating' :
                    $result = $this->validateRatingData($value);
                    $resultData[$field] = is_numeric($result) ? (int)$result : $result;
                    break;
                case 'author_name' :
                    $resultData[$field] = $this->severalLanguagesCheck($value);
                    break;
                case 'comment' :
                    $result = $this->pregMatchStrLen('/.{2,300}/u', $value);
                    if ($result !== '') {
                        if ($this->checkBadWordsInComment($result) === '') {
                            $resultData['bad_comment'] = '';
                        } else {
                            $resultData[$field] = $result;
                        }
                    } else {
                        $resultData[$field] = $result;
                    }

                    break;
                default :
                    throw new InvalidArgumentException(
                        'Wrong field name in provided data array!'
                    );
            }
        }

        return $resultData;
    }

    protected function checkProductInDb(): void
    {
        $dbResult = $this->getDb()->select(['id'])->
            from(['books_catalog'])->
            condition(['id' => $this->productId])->
            query()->fetch();
        if (!$dbResult) {
            throw new \Exception(
                'Didn\'t find product in DB `books_catalog` table using id ' . $this->productId . ' !'
            );
        }
    }

    protected function getDb(): MySqlDbWorkModel
    {
        if (!$this->db) {
            $this->db = MySqlDbWorkModel::getInstance();
        }

        return $this->db;
    }

    protected function validateRatingData(string $ratingQuantity): string
    {
        if (!is_numeric($ratingQuantity) || !$this->validateRatingQuantity($ratingQuantity)) {
            return '';
        }

        return $ratingQuantity;
    }

    protected function validateRatingQuantity(string $ratingQuantity)
    {
        switch ($ratingQuantity) {
            case '0' :
            case '1' :
            case '2' :
            case '3' :
            case '4' :
            case '5' :
                return true;
            default:
                return false;
        }
    }

    protected function severalLanguagesCheck(string $dataString): string
    {
        if ($this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $dataString) === '') {
            return $this->pregMatchStrLen('/[A-Za-z]{2,30}/u', $dataString);
        } else {
            return $this->pregMatchStrLen('/[А-Яа-я]{2,30}/u', $dataString);
        }
    }

    protected function pregMatchStrLen(string $pattern, string $dataString): string
    {
        preg_match($pattern, $dataString, $matches);
        $result = '';
        if (isset($matches[0])) {
            $result = strlen($dataString) === strlen($matches[0]) ? $matches[0] : $result;
        }

        return $result;
    }

    protected function checkBadWordsInComment(string $comment): string
    {
        $patterns = [
            '/хуйня/u',
            '/долбаёб/u'
        ];
        foreach ($patterns as $pattern) {
            preg_match($pattern, strtolower($comment), $matches);
            if (isset($matches[0])) {
                return '';
            }
        }

        return $comment;
    }

    public function returnProductId(): int
    {
        return $this->productId;
    }

    public function returnProductOption(): string
    {
        return $this->productOption;
    }
}
