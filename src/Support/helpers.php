<?php

use Illuminate\Http\JsonResponse;
use O21\RestfulForLaravel\ResponseBuilder;

if (! function_exists('restful')) {
    function restful(
        mixed $data = [],
        array $errors = [],
        ?int $status = null,
        array $headers = [],
        int $options = 0,
        ?callable $fallback = null,
    ): JsonResponse {
        if (is_callable($data)) {
            try {
                $data = app()->call($data);
                $status ??= 200;
            } catch (Throwable $e) {
                if (! $fallback) {
                    throw $e;
                }

                report($e);

                $toErrors = fn (
                    array $errors,
                    int $status = 500,
                    array $headers = [],
                    int $options = 0
                ) => restful(
                    errors: $errors,
                    status: $status,
                    headers: $headers,
                    options: $options,
                );

                return app()->call($fallback, compact('e', 'toErrors'));
            }
        }

        $status = $status ?? (empty($errors) ? 200 : 400);

        /** @var ResponseBuilder $builder */
        $builder = app()->make(ResponseBuilder::class);

        if ($status >= 200 && $status <= 299) {
            return $builder->success($data, $status, $headers, $options);
        }

        return $builder->error($errors, $status, $headers, $options);
    }
}
