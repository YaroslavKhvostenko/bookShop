<?php

namespace Interfaces\User;

interface UserDataValidatorInterface
{
    public function emptyCheck(array $data, string $type): array;

    public function correctCheck(array $data): array;
}