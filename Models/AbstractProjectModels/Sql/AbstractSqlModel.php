<?php

namespace Models\AbstractProjectModels\Sql;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Logger;
use Models\ProjectModels\Message\User\DefaultMsgModel;

abstract class AbstractSqlModel
{
    protected ?Logger $logger = null;
    protected ?AbstractBaseMsgModel $msgModel = null;

    public function setMessageModel(AbstractBaseMsgModel $msgModel): void
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
        $this->getMsgModel()->setErrorMsg();
    }

    protected function getMsgModel(): AbstractBaseMsgModel
    {
        if (!$this->msgModel) {
            $this->setMessageModel(new DefaultMsgModel());
        }

        return $this->msgModel;
    }
}
