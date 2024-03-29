<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\User\User\Add;

use Models\AbstractProjectModels\Validation\Data\User\User\Add\AbstractValidator;

class Validator extends AbstractValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function emptyCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'address' :
                    $resultData[$field] = !$value ? false : $value;
                    break;
                case 'phone' :
                    $resultData[$field] = (!$value || $value === '+380') ? false : $value;
                    break;
                default:
                    throw new \Exception(
                        'Unknown field :' . "'$field'" . 'during emptyCheck data of Add validation!'
                    );
            }
        }

        return $resultData;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function correctCheck(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'phone' :
                    $resultData[$field] = $this->pregMatchStrLen('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $value);
                    break;
                case 'address' :
                    $resultData[$field] = $this->pregMatchStrLen('/.{10,100}/u', $value);
                    break;
                default :
                    throw new \Exception(
                        'Unknown field :' . "'$field'" . 'during correctCheck data of Add validation!'
                    );
            }
        }

        return $resultData;
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

    /**
     * @param string $fieldName
     * @return string|null
     * @throws \Exception
     */
    public function validateFieldName(string $fieldName): ?string
    {
        switch ($fieldName) {
            case 'address' :
            case 'phone' :
                return $fieldName;
            default:
                throw new \Exception(
                    'Unknown field :' . "'$fieldName'" . 'from URI string,
                     during Add validation in validateFieldName method!'
                );
        }
    }

    public function compareFieldNames(string $fieldName, array $data = null): void
    {
        if ($this->validateFieldName($fieldName) === $fieldName) {
            if (!array_key_exists($fieldName, $data)) {
                throw new \InvalidArgumentException(
                    'Field \'' . $fieldName . '\' does not exist in the provided data array.'
                );
            }
        }
    }
}
