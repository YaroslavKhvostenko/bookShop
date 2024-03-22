<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Order\Create;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Validation\Data\Order\AbstractBaseValidator;

abstract class AbstractValidator extends AbstractBaseValidator
{
    public function emptyCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'customer_name' :
                case 'customer_email' :
                case 'customer_address' :
                    $resultData[$field] = !$value ? '' : $value;
                    break;
                case 'customer_phone' :
                    $resultData[$field] = !$value || $value === '+380' ? '' : $value;
                    break;
                case 'order_comment' :
                    if ($value) {
                        $resultData[$field] = $value;
                    }

                    break;
                default:
                    throw new InvalidArgumentException(
                        'Field \'' . $field . '\' does not exist in the provided data array.'
                    );
            }
        }

        return $resultData;
    }


    public function correctCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'customer_name' :
                    $resultData[$key] = $this->severalLanguagesCheck($value);
                    break;
                case 'customer_email' :
                    $resultData[$key] = $this->checkEmail($value);
                    break;
                case 'customer_phone' :
                    $resultData[$key] = $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                    break;
                case 'customer_address' :
                case 'order_comment' :
                    $resultData[$key] = $this->pregMatchStrLen('/.{10,100}/u', $value);
                    break;
                default :
                    throw new InvalidArgumentException(
                        'Field \'' . $key . '\' does not exist in the provided data array.'
                    );
            }
        }

        return $resultData;
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

    protected function checkEmail(string $emailString): string
    {
        return filter_var($emailString, FILTER_VALIDATE_EMAIL) ? $emailString : '';
    }
}
