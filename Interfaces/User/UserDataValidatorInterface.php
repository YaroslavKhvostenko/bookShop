<?php

namespace Interfaces\User;

interface UserDataValidatorInterface
{
    public function emptyCheck(array $data): array;

    public function correctCheck(array $data): array;
}
