<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message;

use Models\AbstractProjectModels\Session\Message\AbstractSessionModel;
use Models\ProjectModels\Session\Message\SessionModel;

abstract class AbstractBaseMsgModel
{
    protected ?AbstractSessionModel $msgSessModel = null;
    protected const USER_ESSENCE = 'user';
    protected const PROJECT = 'project';
    protected const DEFAULT = 'default';
    private const DEFAULT_ERROR_MSG = 'Произошла ошибка на нашей стороне.Приносим наши извинения!';
    protected const FILE_ERR = 'file';
    private const FILE_DOWNLOADING_ERR_MSG = 'Неудалось загрузить файл! Попробуйте загрузку файла позже!';
    protected const HACKING_ATTEMPT = 'hack';
    private const HACK_MSG = 'Не надо пытаться взломать наш сайт!)';
    protected const EMPTY = 'empty';
    protected const WRONG = 'wrong';
    protected const SUCCESS_RESULT = 'success';
    protected const FAILURE_RESULT = 'failure';
    private const PROJECT_ERRORS = [
        self::DEFAULT => self::DEFAULT_ERROR_MSG,
        self::HACKING_ATTEMPT => self::HACK_MSG,
        self::FILE_ERR => self::FILE_DOWNLOADING_ERR_MSG
    ];

    protected array $messages = [
        self::EMPTY => self::EMPTY_DATA,
        self::WRONG => self::WRONG_DATA,
        self::SUCCESS_RESULT => self::SUCCESS_MSGS,
        self::FAILURE_RESULT => self::FAILURE_MSGS,
        self::PROJECT => self::PROJECT_ERRORS
    ];
    private const EMPTY_DATA = [];
    private const WRONG_DATA = [];
    private const FAILURE_MSGS = [];
    private const SUCCESS_MSGS = [];

    /**
     * @param string $messagesType
     * @param string|null $msgType
     * @return string
     * @throws \Exception
     */
    protected function getMessage(string $messagesType, string $msgType = null): string
    {
        if (!array_key_exists($messagesType, $this->messages)) {
            throw new \Exception('Message for \'messagesType\' - \'' . $messagesType . '\' does not exist!');
        } elseif ($msgType !== null && !array_key_exists($msgType, $this->messages[$messagesType])) {
            throw new \Exception('Message for \'msgType\' - \'' . $msgType . '\' does not exist!');
        } else {

            return $this->messages[$messagesType][$msgType];
        }
    }

    /**
     * @param string $messagesType
     * @param string|null $msgType
     * @param string|null $fieldName
     * @throws \Exception
     */
    public function setMessage(string $messagesType, string $msgType = null, string $fieldName = null): void
    {
        $this->getSessionModel()->setMessage($this->getMessage($messagesType, $msgType), $fieldName);
    }

    public function deleteAllMessages(): void
    {
        $this->getSessionModel()->deleteAllMessages();
    }

    /**
     * @param string $errorType
     * @throws \Exception
     */
    public function setErrorMsg(string $errorType = self::DEFAULT): void
    {
        $this->deleteAllMessages();
        $this->setMessage(self::PROJECT, $errorType, $errorType);
    }

    protected function getSessionModel(): AbstractSessionModel
    {
        if (!$this->msgSessModel) {
            $this->msgSessModel = SessionModel::getInstance();
        }

        return $this->msgSessModel;
    }
}
