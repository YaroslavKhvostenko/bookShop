<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Session\Message;

use Models\AbstractProjectModels\Session\AbstractSessionModel as BaseSessionModel;

abstract class AbstractSessionModel extends BaseSessionModel
{
    protected const SESS_FIELD = 'resultMsg';

    public static function getInstance(): AbstractSessionModel
    {
        return static::createSelf();
    }

    public function getMessages(): ?array
    {
        if ($this->data !== null) {
            $this->deleteAllMessages();
        }

        return $this->data;
    }

    public function deleteAllMessages(): void
    {
        if ($this->data !== null) {
            $this->deleteData(self::getSessField());
        }
    }

    public function setMessage(string $msg, string $fieldName = null): void
    {
        $this->setData($msg, $fieldName);
        if($fieldName !== null) {
            $this->data[$fieldName] = $msg;
        } else {
            $this->data[] = $msg;
        }
    }
}
