<?php
declare(strict_types=1);

namespace Views\AbstractViews;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Session\User\AbstractSessionModel as UserSessionModel;

abstract class AbstractCatalogView extends AbstractDefaultView
{
    protected const PUB_MONTHS = [
        'Января',
        'Февраля',
        'Марта',
        'Апреля',
        'Мая',
        'Июня',
        'Июля',
        'Августа',
        'Сентября',
        'Октября',
        'Ноября',
        'Декабря'
    ];

    public function __construct(UserSessionModel $userSessModel)
    {
        parent::__construct($userSessModel);
    }

    protected function getPubDate(string $pubDate): ?string
    {
        $pubDate = explode(".", $pubDate);
        if (!isset(self::PUB_MONTHS[(int)$pubDate[1]])) {
            throw new InvalidArgumentException(
                'Very strange number of month ' . $pubDate[1] . ' !'
            );
        }

        $day = $pubDate[0];
        $month = self::PUB_MONTHS[(int)$pubDate[1]];
        $year = $pubDate[2];

        return $day . ' ' . $month . ' ' . $year . ' года';
    }
}
