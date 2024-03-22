<?php
declare(strict_types=1);

namespace Models\ProjectModels\Session;

use Interfaces\IDataManagement;

class Manager implements IDataManagement
{
    private const USER_KEY = 'user';
    private const MESSAGE_KEY = 'resultMsg';
    private const SESS_FIELDS = [
        self::USER_KEY => self::USER_KEY,
        self::MESSAGE_KEY => self::MESSAGE_KEY,
        'filter' => 'filter'
    ];

    public function getData(string $fieldName): ?array
    {
        if (array_key_exists($fieldName, self::SESS_FIELDS)) {
            return $_SESSION[$fieldName] ?? null;
        } else {
            throw new \InvalidArgumentException(
                'Wrong $fieldName name in $_SESSION array, 
                during trying to get data from $_SESSION array!'
            );
        }
    }

    public function unsetData(string $sessionField, string $dataField = null): void
    {
        if (array_key_exists($sessionField, $_SESSION)) {
            if ($dataField !== null) {
                if (array_key_exists($dataField, $_SESSION[$sessionField])) {
                    unset($_SESSION[$sessionField][$dataField]);
                } else {
                    throw new \InvalidArgumentException(
                        'Wrong $dataField name in $_SESSION[$sessionField] array,
                         during trying to unset data in $_SESSION[$sessionField] array!'
                    );
                }
            } else {
                unset($_SESSION[$sessionField]);
            }
        } else {
            throw new \InvalidArgumentException(
                'Wrong $sessionField name in $_SESSION array, 
                during trying to unset data in $_SESSION array!'
            );
        }
    }

    public function setData(string $sessionField, string $data, string $dataField = null): void
    {
        if (array_key_exists($sessionField, self::SESS_FIELDS)) {
            if ($dataField !== null) {
                $_SESSION[$sessionField][$dataField] = $data;
            } else {
                $_SESSION[$sessionField][] = $data;
            }
        } else {
            throw new \InvalidArgumentException(
                'Wrong $sessionField name in $_SESSION array, 
                during trying to set data in $_SESSION array!'
            );
        }
    }

    public function destroySession(): void
    {
        session_destroy();
    }
}
