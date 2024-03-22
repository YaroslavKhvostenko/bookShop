<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message\Filter\Set;

use Models\AbstractProjectModels\Message\Filter\AbstractBaseMsgModel;

abstract class AbstractMsgModel extends AbstractBaseMsgModel
{
    private const EMPTY_FILTER_CATALOG_SHOW = 'Вы забыли выбрать параметры для фильрации книг в каталоге!';
    private const WRONG_FILTER_CATALOG_SHOW = 'Неправильные параметры фильтрации! Следуйте рекомендациям!';
    private const EMPTY_FILTER_CATALOG_SHOW_PRICE = 'Вы не указали стоимость для выборки с использованием стоимости!';
    private const EMPTY_FILTER_CATALOG_SHOW_RATING = 'Вы не указали значение рейтинга для выборки с использованием рейтинга!';
    private const EMPTY_FILTER_CATALOG_SHOW_QUANTITY = 'Вы не указали количество книг для выборки с использованием количества книг!';
    private const EMPTY_FILTER_CATALOG_SHOW_PRICE_OPERATOR = 'Вы не выбрали больше,меньше или равно для выборки по цене!';
    private const EMPTY_FILTER_CATALOG_SHOW_RATING_OPERATOR = 'Вы не выбрали больше,меньше или равно для выборки по рейтингу!';
    private const EMPTY_FILTER_CATALOG_SHOW_QUANTITY_OPERATOR = 'Вы не выбрали больше,меньше или равно для выборки по количеству!';
    private const WRONG_FILTER_CATALOG_SHOW_PRICE = 'Неправильный формат введенной количества стоимости книг!';
    private const WRONG_FILTER_CATALOG_SHOW_RATING = 'Неправильный формат введенного количества рейтинга книг!';
    private const WRONG_FILTER_CATALOG_SHOW_QUANTITY = 'Неправильный формат введенного количества книг!';
    private const EMPTY_DATA = [
        'empty_filter_catalog_show' => self::EMPTY_FILTER_CATALOG_SHOW,
        'empty_filter_catalog_show_price_operator' => self::EMPTY_FILTER_CATALOG_SHOW_PRICE_OPERATOR,
        'empty_filter_catalog_show_rating_operator' => self::EMPTY_FILTER_CATALOG_SHOW_RATING_OPERATOR,
        'empty_filter_catalog_show_quantity_operator' => self::EMPTY_FILTER_CATALOG_SHOW_QUANTITY_OPERATOR,
        'empty_filter_catalog_show_price' => self::EMPTY_FILTER_CATALOG_SHOW_PRICE,
        'empty_filter_catalog_show_rating' => self::EMPTY_FILTER_CATALOG_SHOW_RATING,
        'empty_filter_catalog_show_quantity' => self::EMPTY_FILTER_CATALOG_SHOW_QUANTITY,
    ];
    private const WRONG_DATA = [
        'wrong_filter_catalog_show' => self::WRONG_FILTER_CATALOG_SHOW,
        'wrong_filter_catalog_show_price' => self::WRONG_FILTER_CATALOG_SHOW_PRICE,
        'wrong_filter_catalog_show_rating' => self::WRONG_FILTER_CATALOG_SHOW_RATING,
        'wrong_filter_catalog_show_quantity' => self::WRONG_FILTER_CATALOG_SHOW_QUANTITY,
    ];

    public function __construct()
    {
        $this->messages['empty'] = self::EMPTY_DATA;
        $this->messages['wrong'] = self::WRONG_DATA;
    }
}
