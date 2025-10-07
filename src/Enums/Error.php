<?php

namespace O21\RestfulForLaravel\Enums;

use O21\RestfulForLaravel\Contracts\EnumError;

enum Error implements EnumError
{
    case Internal;
    case Generic;
    case Unknown;

    public function message(): string
    {
        return match ($this) {
            self::Internal => 'Internal server error.',
            self::Generic => 'An error occurred.',
            self::Unknown => 'An unknown error occurred.',
        };
    }
}
