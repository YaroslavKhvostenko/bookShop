<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Message;

use Models\AbstractProjectModels\AbstractFactory;

abstract class AbstractMsgModelsFactory extends AbstractFactory
{
    protected const NAME_SPACE = 'Models\ProjectModels\Message\\';

    public function __construct()
    {

    }

    protected static function isDefault(string $customerType): ?string
    {
        $path = '\\';
        if ($customerType === 'default') {
            $path = null;
        }

        return $path;
    }

    abstract public static function getMsgModel(string $customerType, string $actionType = null): AbstractBaseMsgModel;
}