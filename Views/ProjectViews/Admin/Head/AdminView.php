<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin\Head;

use http\Exception\InvalidArgumentException;
use Views\AbstractViews\AbstractAdminView;

class AdminView extends AbstractAdminView
{
    protected const HEAD_ADMIN_LAYOUTS = 'head/';
    private const PROVIDE = 'provide';
    private const REMOVE = 'remove';
    private const REDIRECT = 'redirect';
    private const ACCESS = 'access';
    private const APPROVE = 'approve';
    private const CANCEL = 'cancel';
    private const HEAD = 'head';
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

    protected function getContentPath(): string
    {
//        return $this->getPath() . $this->getAdminPath() . self::HEAD_ADMIN_LAYOUTS;
        return $this->getPath() . $this->getAdminPath();
    }

    protected function getHeaderPath(): string
    {
//        $headerPath = parent::getHeaderPath() . $this->getAdminPath();
//        if ($this->userSessModel->isHeadAdmin()) {
//            $headerPath .= 'head/';
//        }
//
//        return $headerPath;
        return parent::getHeaderPath() . $this->getAdminPath();
    }

    public function getFormRequestAction(): string
    {
        if (!array_key_exists($this->getRequestAction(), self::ACTIONS)) {
            throw new InvalidArgumentException(
                'Wrong action name, probably you forgot to add it in const ACTIONS!'
            );
        }

        return self::ACTIONS[$this->getRequestAction()];
    }

    public function getDbFieldName(): string
    {
        if (!array_key_exists($this->getRequestAction(), self::DB_FIELDS)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const DB_FIELDS!'
            );
        }

        return self::DB_FIELDS[$this->getRequestAction()];
    }

    public function getTableFieldFormValue(): string
    {
        if (!array_key_exists($this->getRequestAction(), self::TABLE_FORM_FIELD_VALUES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const TABLE_FORM_FIELD_VALUES!'
            );
        }

        return self::TABLE_FORM_FIELD_VALUES[$this->getRequestAction()];
    }

    public function getCheckBoxValue(string $adminId = null): string
    {
        return $adminId . '/' . $this->getDbFieldName() . '/' . $this->getDbFieldValue();
    }

    private function getDbFieldValue(): string
    {
        if (!array_key_exists($this->getRequestAction(), self::DB_FIELDS_VALUES)) {
            throw new InvalidArgumentException(
                'Wrong field name, probably you forgot to add it in const DB_FIELDS_VALUES!'
            );
        }

        return self::DB_FIELDS_VALUES[$this->getRequestAction()];
    }

    protected function getAdminPath(): string
    {
        return parent::getAdminPath() . self::HEAD_ADMIN_LAYOUTS;
    }
}
