<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message;

use Interfaces\IDataManagement;
use Models\ProjectModels\DataRegistry;

abstract class AbstractBaseMsgModel
{
    protected IDataManagement $sessionInfo;
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

    public function __construct()
    {
        $this->sessionInfo = DataRegistry::getInstance()->get('session');
    }

    /**
     * @param string $messagesType
     * @param string|null $msgType
     * @return string
     * @throws \Exception
     */
    public function getMessage(string $messagesType, string $msgType = null): string
    {
        if (!array_key_exists($messagesType, $this->messages)) {
            throw new \Exception('Message for \'messagesType\' - \'' . $messagesType . '\' does not exist!');
        } elseif ($msgType !== null && !array_key_exists($msgType, $this->messages[$messagesType])) {
            throw new \Exception('Message for \'msgType\' - \'' . $msgType . '\' does not exist!');
        } else {

            return $this->messages[$messagesType][$msgType];
        }
    }

    public function setMsg(string $msg, string $fieldName = null): void
    {
        $this->sessionInfo->setSessionMsg($msg, $fieldName);
    }

    public function unsetMessages(): void
    {
        $this->sessionInfo->unsetAllMessages();
    }

    /**
     * @param string|null $errorType
     * @throws \Exception
     */
    public function errorMsgSetter(string $errorType = self::DEFAULT): void
    {
        $this->unsetMessages();
        $this->setMsg($this->getMessage(self::PROJECT, $errorType), $errorType);
    }
}
