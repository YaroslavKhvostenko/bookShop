<?php

namespace Models\AbstractProjectModels\Sql;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Logger;
use Models\ProjectModels\Message\User\DefaultMsgModel;

abstract class AbstractSqlModel
{
    protected ?Logger $logger = null;
    protected ?AbstractBaseMsgModel $msgModel = null;

    abstract public function selectData(string $tableName, array $field, array $condition = null);

    abstract public function insertData(string $tableName, array $data);

    public function setMsgModel(AbstractBaseMsgModel $msgModel): void
    {
        if (!$this->msgModel || $this->msgModel instanceof DefaultMsgModel) {
            $this->msgModel = $msgModel;
        }
    }

    protected function getLogger(): Logger
    {
        if (!$this->logger) {
            $this->logger = Logger::getInstance();
        }

        return $this->logger;
    }

    /**
     * @param \Exception $exception
     * @param string|null $msg
     * @throws \Exception
     */
    protected function catchException(\Exception $exception, string $msg = null): void
    {
        $this->getLogger()->logException($exception, $msg);
        $this->getMsgModel()->errorMsgSetter();
    }

    protected function getMsgModel(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->setMsgModel(new DefaultMsgModel());
        }

        return $this->msgModel;
    }
}
