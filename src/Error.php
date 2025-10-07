<?php

namespace O21\RestfulForLaravel;

use O21\RestfulForLaravel\Contracts\TransformableError;

class Error implements TransformableError
{
    public function __construct(
        protected string $code,
        protected string $message,
        protected array $meta = [],
    ) {}

    public function code(): string
    {
        return $this->code;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function meta(): array
    {
        return $this->meta;
    }
}
