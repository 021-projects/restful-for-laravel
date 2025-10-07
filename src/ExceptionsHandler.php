<?php

namespace O21\RestfulForLaravel;

use Illuminate\Validation\ValidationException;
use O21\RestfulForLaravel\Enums\Error;
use O21\RestfulForLaravel\Exceptions\RestfulException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionsHandler
{
    protected array $dontRender = [];

    public function render(\Throwable $e): Response
    {
        if (in_array($e::class, $this->dontRender, true)) {
            throw $e;
        }

        if ($e instanceof RestfulException) {
            return restful(
                errors: $e->getErrors(),
                status: $e->getStatusCode(),
                headers: $e->getHeaders(),
            );
        }

        if ($e instanceof HttpException) {
            return restful(
                errors: [
                    [
                        'message' => $e->getMessage()
                            ?: Response::$statusTexts[$e->getStatusCode()]
                            ?? Error::Unknown->message(),
                        'code' => 'Exception:'.class_basename($e),
                    ],
                ],
                status: $e->getStatusCode(),
                headers: $e->getHeaders(),
            );
        }

        if ($e instanceof ValidationException) {
            $errors = [];

            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'message' => $message,
                        'code' => 'Invalid:'.$field,
                        'meta' => compact('field'),
                    ];
                }
            }

            return restful(
                errors: $errors,
                status: 422,
            );
        }

        return restful(
            errors: [Error::Internal],
            status: 500,
        );
    }

    public function dontRenderExceptions(array $exceptions): void
    {
        $this->dontRender = array_values(
            array_unique(array_merge($this->dontRender, $exceptions))
        );
    }
}
