<?php

use Illuminate\Http\JsonResponse;

class FooBarController
{
    public function bar(): JsonResponse
    {
        return restful(
            data: [
                'message' => 'Hello, World!',
            ]
        );
    }

    public function barErrors(): JsonResponse
    {
        return restful(
            errors: [
                new \O21\RestfulForLaravel\Error(
                    code: 'SIMPLE_ERROR',
                    message: 'This is a simple error message.',
                    meta: [
                        'ts' => time(),
                    ],
                ),
                // Array errors also works
                [
                    'code' => 'ANOTHER_ERROR',
                    'message' => 'This is another error message.',
                    'meta' => [
                        'ts' => time(),
                    ],
                ],
                // Will be converted to ['message' => 'Just a string error message', 'code' => 'Error:Generic']
                'Just a string error message',
            ]
        );
    }

    public function barWithFallback(): JsonResponse
    {
        return restful(
            data: function () {
                throw new \Exception('Live site is down!');
            },
            fallback: function (Throwable $e, callable $toErrors) {
                if ($e->getMessage() === 'Live site is down!') {
                    return restful(
                        data: cache()->get('last_known_good_response', [])
                    );
                }

                return $toErrors(
                    errors: [
                        new \O21\RestfulForLaravel\Error(
                            code: 'SERVICE_UNAVAILABLE',
                            message: 'The service is currently unavailable. Please try again later.',
                        ),
                    ],
                    status: 503,
                );
            },
        );
    }

    public function barCreated(): JsonResponse
    {
        return restful(
            data: [
                'id' => 123,
                'message' => 'Resource created successfully.',
            ],
            status: 201,
        );
    }

    public function barWithCookieAndHeaders(): JsonResponse
    {
        return restful(
            data: [
                'message' => 'Hello with cookie!',
            ],
            headers: [
                'Foo' => 'Bar',
            ],
        )->withCookie(
            cookie('TestCookie', 'CookieValue', 60)
        );
    }
}
