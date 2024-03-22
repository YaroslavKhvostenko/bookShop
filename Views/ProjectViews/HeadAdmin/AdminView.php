<?php
declare(strict_types=1);

namespace Views\ProjectViews\HeadAdmin;

use http\Exception\InvalidArgumentException;
use Views\AbstractViews\Admin\AbstractAdminView;
use Models\ProjectModels\Session\HeadAdmin\SessionModel;

class AdminView extends AbstractAdminView
{
    private const PROVIDE = 'provide';
    private const REMOVE = 'remove';
    private const REDIRECT = 'redirect';
    private const ACCESS = 'access';
    protected const ACTIONS = [
        self::PROVIDE => self::ACCESS,
        self::REMOVE => self::ACCESS,
        self::REDIRECT => self::ACCESS
    ];
    private const TABLE_FORM_FIELD_VALUES = [
        self::PROVIDE => 'Предоставить доступ',
        self::REMOVE => 'Отобрать доступ',
        self::REDIRECT => 'Передать должность'
    ];
    private const DB_FIELDS = [
        self::PROVIDE => 'is_approved',
        self::REMOVE => 'is_approved',
        self::REDIRECT => 'is_head'
    ];
    private const DB_FIELDS_VALUES = [
        self::PROVIDE => '1',
        self::REMOVE => '0',
        self::REDIRECT => '1'
    ];

    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    public function getFormRequestAction(): string
    {
        if (!array_key_exists($this->serverInfo->getRequestAction(), self::ACTIONS)) {
            throw new InvalidArgumentException(
                'Wrong action name, probably you forgot to add it in const ACTIONS!'
            );
        }

        return self::ACTIONS[$this->serverInfo->getRequestAction()];
    }

    public function getDbFieldName(): string
    {
        if (!array_key_exists($this->serverInfo->getRequestAction(), self::DB_FIELDS)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const DB_FIELDS!'
            );
        }

        return self::DB_FIELDS[$this->serverInfo->getRequestAction()];
    }

    public function getTableFieldFormValue(): string
    {
        if (!array_key_exists($this->serverInfo->getRequestAction(), self::TABLE_FORM_FIELD_VALUES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const TABLE_FORM_FIELD_VALUES!'
            );
        }

        return self::TABLE_FORM_FIELD_VALUES[$this->serverInfo->getRequestAction()];
    }

    public function getCheckBoxValue(string $adminId = null): string
    {
        return $adminId . '/' . $this->getDbFieldName() . '/' . $this->getDbFieldValue();
    }

    private function getDbFieldValue(): string
    {
        if (!array_key_exists($this->serverInfo->getRequestAction(), self::DB_FIELDS_VALUES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const DB_FIELDS_VALUES!'
            );
        }

        return self::DB_FIELDS_VALUES[$this->serverInfo->getRequestAction()];
    }
}
