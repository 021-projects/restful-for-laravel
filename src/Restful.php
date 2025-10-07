<?php

namespace O21\RestfulForLaravel;

class Restful
{
    public static function dontRenderExceptions(array $exceptions): void
    {
        app()->make(ExceptionsHandler::class)
            ->dontRenderExceptions($exceptions);
    }
}
