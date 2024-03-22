<?php
declare(strict_types=1);

namespace Models\ProjectModels\Validation\Data\Task\HeadAdmin\Create;

use Models\AbstractProjectModels\Validation\Data\Task\AbstractValidator;
use mysql_xdevapi\Exception;

class Validator extends AbstractValidator
{
    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function checkData(array $data): array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'task_description' :
                    $resultData[$field] = $this->pregMatchStrLen('/.{10,200}/u', $value);
                    break;
                case 'admin_id' :
                    $resultData[$field] = (int)$this->checkAdminId($value);
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
     * @param string $adminId
     * @return string|null
     * @throws \Exception
     */
    protected function checkAdminId(string $adminId): ?string
    {
        if (!is_numeric($adminId)) {
            throw new Exception(
                'Field `admin_id` have to be numeric, like \'1\'.' .
                'Probably you sent empty string or string with word! ' .
                'You sent : ' . "'$adminId'" .
                'Check what comes from the form!!!'
            );
        }

        $result = $this->getDb()->select(['id'])->from(['admins'])->condition(['id' => $adminId])->query()->fetch();
        if (!$result) {
            throw new Exception(
                'Very strange that admin was not found in DB, 
                because admin_id was take from DB to fill the select form on \'create_task.phtml\'!'
            );
        }

        return $adminId;
    }
}
