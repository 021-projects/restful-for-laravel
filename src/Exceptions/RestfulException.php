<?php

namespace O21\RestfulForLaravel\Exceptions;

class RestfulException extends \RuntimeException
{
    public function __construct(
        protected array $errors = [],
        protected int $statusCode = 400,
        protected array $headers = [],
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function withErrors(
        array $errors,
        int $statusCode = 400,
        array $headers = [],
    ): static {
        return new static(
            errors: $errors,
            statusCode: $statusCode,
            headers: $headers,
        );
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
