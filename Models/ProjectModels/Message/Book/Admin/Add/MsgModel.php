<?php
declare(strict_types=1);

namespace Models\ProjectModels\Message\Book\Admin\Add;

use Models\AbstractProjectModels\Message\Book\AbstractBaseMsgModel;

class MsgModel extends AbstractBaseMsgModel
{
    private const NO_AUTHORS = 'На данный момент нет ни одного автора, добавленного в базу данных!';
    private const NO_GENRES = 'На данный момент нет ни одного жанра, добавленного в базу данных!';
    private const EMPTY_AUTHORS = 'На данный момент нет ни одного автора. Добавьте сперва автора в базу данных!';
    private const EMPTY_GENRES = 'На данный момент нет ни одного жанра. Добавьте сперва жанр в базу данных!';
    private const EMPTY_TITLE = 'Вы забыли указать название!';
    private const EMPTY_NAME = 'Вы забыли указать имя автора!';
    private const EMPTY_AUTHOR_ID = 'Вы забыли выбрать автора!';
    private const EMPTY_GENRE_ID = 'Вы забыли выбрать жанр!';
    private const EMPTY_PUB_DATE = 'Вы забыли указать дату публикации!';
    private const EMPTY_DESCRIPTION = 'Вы забыли написать описание!!';
    private const EMPTY_PRICE = 'Вы забыли указать стоимость!';
    private const EMPTY_QUANTITY = 'Вы забыли указать количество!';
    private const EMPTY_IMAGE = 'Вы забыли выбрать картинку книги!';
    private const WRONG_TITLE = 'Неправильный формат названия!';
    private const WRONG_NAME = 'Неправильный формат имени автора! Только кириллица!';
    private const WRONG_DESCRIPTION = 'Неправильный формат описания книги!Только кириллица и от 50 до 300 символов!';
    private const WRONG_PUB_DATE = 'Неправильный формат даты публикации! Используйте дд.мм.гг = 01.01.2000';
    private const WRONG_AUTHOR_ID = 'Удивительное дело! Мы потеряли своего же Автора! Срочно помогите найти!)))';
    private const WRONG_GENRE_ID = 'Удивительное дело! Мы потеряли жанр! Срочно помогите найти!)))';
    private const WRONG_PRICE = 'Неправильный формат цены! Только цифры!';
    private const WRONG_QUANTITY = 'Неправильный формат количества! Только цифры!';
    private const WRONG_PRICE_ZERO = 'Ну какой смысл вводить стоимость 0 !?';
    private const WRONG_QUANTITY_ZERO = 'Ну какой смысл вводить количество 0!?';
    private const WRONG_IMAGE_SIZE = 'Слишком большая картинка! Не более 1гб!';
    private const WRONG_IMAGE_TYPE = 'Неправильный формат картинки! Только jpeg или png!';
    private const BOOK_EXIST = 'Невозможно добавить книгу! Так как эта книга с таким же названием и автором уже есть!';
    private const GENRE_EXIST = 'Невозможно добавить жанр! Так как этот жанр с таким же названием уже есть!';
    private const AUTHOR_EXIST = 'Невозможно добавить автора! Так как автор с таким же именем уже есть!';
    private const SUCCESS_BOOK = 'Книга была успешно добавлена!';
    private const SUCCESS_GENRE = 'Жанр был успешно добавлен!';
    private const SUCCESS_AUTHOR = 'Автор был успешно добавлен!';
    private const EMPTY_DATA = [
        'no_authors' => self::NO_AUTHORS,
        'no_genres' => self::NO_GENRES,
        'empty_genres' => self::EMPTY_GENRES,
        'empty_authors' => self::EMPTY_AUTHORS,
        'title' => self::EMPTY_TITLE,
        'name' => self::EMPTY_NAME,
        'author_id' => self::EMPTY_AUTHOR_ID,
        'genre_id' => self::EMPTY_GENRE_ID,
        'pub_date' => self::EMPTY_PUB_DATE,
        'description' => self::EMPTY_DESCRIPTION,
        'price' => self::EMPTY_PRICE,
        'quantity' => self::EMPTY_QUANTITY,
        'image' => self::EMPTY_IMAGE
    ];
    private const WRONG_DATA = [
        'title' => self::WRONG_TITLE,
        'name' => self::WRONG_NAME,
        'description' => self::WRONG_DESCRIPTION,
        'pub_date' => self::WRONG_PUB_DATE,
        'author_id' => self::WRONG_AUTHOR_ID,
        'genre_id' => self::WRONG_GENRE_ID,
        'price' => self::WRONG_PRICE,
        'quantity' => self::WRONG_QUANTITY,
        'price_zero' => self::WRONG_PRICE_ZERO,
        'quantity_zero' => self::WRONG_QUANTITY_ZERO,
        'image_size' => self::WRONG_IMAGE_SIZE,
        'image_type' => self::WRONG_IMAGE_TYPE,
    ];
    private const FAILURE_MSGS = [
        'book_exist' => self::BOOK_EXIST,
        'genre_exist' => self::GENRE_EXIST,
        'author_exist' => self::AUTHOR_EXIST,
    ];
    private const SUCCESS_MSGS = [
        'book' => self::SUCCESS_BOOK,
        'genre' => self::SUCCESS_GENRE,
        'author' => self::SUCCESS_AUTHOR,
    ];

    public function __construct()
    {
        $this->messages[self::EMPTY] = self::EMPTY_DATA;
        $this->messages[self::WRONG] = self::WRONG_DATA;
        $this->messages[self::FAILURE_RESULT] = self::FAILURE_MSGS;
        $this->messages[self::SUCCESS_RESULT] = self::SUCCESS_MSGS;
    }
}
