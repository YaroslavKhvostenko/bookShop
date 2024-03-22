<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use http\Exception\InvalidArgumentException;
use Views\AbstractViews\AbstractBookView;
use Models\ProjectModels\Session\Admin\SessionModel as AdminSessModel;

class BookView extends AbstractBookView
{
    private ?string $param = null;
    private const TITLES = [
        'add' => self::ADD_ACTION_TITLES
    ];
    private const ADD_ACTION_TITLES = [
        'book' => 'Добавить книгу',
        'genre' => 'Добавить жанр',
        'author' => 'Добавить автора'
    ];
    private const PAGES = [
        'add' => self::ADD_ACTION_PAGES
    ];
    private const ADD_ACTION_PAGES = [
        'book' => 'add_book.phtml',
        'genre' => 'add_genre.phtml',
        'author' => 'add_author.phtml'
    ];
    private const LABELS_DATA = [
        'add' => self::ADD_ACTION_LABELS
    ];
    private const ADD_ACTION_LABELS = [
        'genre' => 'Только кириллица!(не больше 50 знаков)',
        'author' => 'Только кирилица(не больше 15 знаков)'
    ];
    private const FIELD_VALUES = [
        'add' => self::ADD_ACTION_FIELD_VALUES
    ];
    private const ADD_ACTION_FIELD_VALUES = [
        'genre' => 'title',
        'author' => 'name'
    ];

    public function __construct()
    {
        parent::__construct(AdminSessModel::getInstance());
    }

    public function setParam(string $param): void
    {
        $this->param = $param;
    }

    public function getParam(): string
    {
        return $this->param;
    }

    public function getTitle(string $actionName): ?string
    {
        if (array_key_exists($actionName, self::TITLES)) {
            if (!array_key_exists($this->param, self::TITLES[$actionName])) {
                throw new InvalidArgumentException(
                    'Wrong param : ' . "'$this->param'" .
                    ', during choosing title from const TITLES to use it for page title!
                    Check this const or what comes here!'
                );
            }
        } else {
            throw new InvalidArgumentException(
                'Wrong action name : ' . "'$actionName'" .
                ', during choosing title from const TITLES to use it for page title!
                Check this const or what comes here!'
            );
        }

        return self::TITLES[$actionName][$this->param];
    }

    public function getInputValue(): ?string
    {
        return $this->getFieldValue();
    }

    public function getFieldName(): ?string
    {
        return $this->getFieldValue();
    }

    private function getFieldValue(): ?string
    {
        $actionName = $this->serverInfo->getRequestAction();
        if (array_key_exists($actionName, self::FIELD_VALUES)) {
            if (!array_key_exists($this->param, self::FIELD_VALUES[$actionName])) {
                throw new InvalidArgumentException(
                    'Wrong param : ' . "'$this->param'" .
                    ', during choosing value from const INPUT_VALUES to use it for input value form!
                    Check this const or what comes here!'
                );
            }
        } else {
            throw new InvalidArgumentException(
                'Wrong action name : ' . "'$actionName'" .
                ', during choosing value from const INPUT_VALUES to use it for input value form!
                Check this const or what comes here!'
            );
        }

        return self::FIELD_VALUES[$actionName][$this->param];
    }

    public function getPage(string $actionName): string
    {
        if (array_key_exists($actionName, self::PAGES)) {
            if (!array_key_exists($this->param, self::PAGES[$actionName])) {
                throw new InvalidArgumentException(
                    'Wrong param : ' . "'$this->param'" .
                    ', during choosing page from const PAGES! Check this const or what comes here!'
                );
            }
        } else {
            throw new InvalidArgumentException(
                'Wrong action name : ' . "'$actionName'" .
                ', during choosing page from const PAGES! Check this const or what comes here!'
            );
        }

        return self::PAGES[$actionName][$this->param];
    }

    public function getLabelData(string $actionName): string
    {
        if (array_key_exists($actionName, self::LABELS_DATA)) {
            if (!array_key_exists($this->param, self::LABELS_DATA[$actionName])) {
                throw new InvalidArgumentException(
                    'Wrong param : ' . "'$this->param'" .
                    ', during choosing label data from const LABELS_DATA! Check this const or what comes here!'
                );
            }
        } else {
            throw new InvalidArgumentException(
                'Wrong action name : ' . "'$actionName'" .
                ', during choosing label data from const LABELS_DATA! Check this const or what comes here!'
            );
        }

        return self::LABELS_DATA[$actionName][$this->param];
    }
}
