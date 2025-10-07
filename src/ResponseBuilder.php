<?php

namespace O21\RestfulForLaravel;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use O21\RestfulForLaravel\Contracts\EnumError;
use O21\RestfulForLaravel\Contracts\TransformableError;
use O21\RestfulForLaravel\Enums\Error;

class ResponseBuilder
{
    public function success(
        mixed $data,
        int $status = 200,
        array $headers = [],
        int $options = 0,
    ): JsonResponse {
        return response()->json($data, $status, $headers, $options);
    }

    public function error(
        array $errors,
        int $status = 400,
        array $headers = [],
        int $options = 0,
    ): JsonResponse {
        return response()->json([
            'errors' => $this->formatErrors($errors),
        ], $status, $headers, $options);
    }

    protected function formatErrors(array $errors): array
    {
        return array_map(function ($error) {
            if (is_string($error)) {
                return [
                    'message' => $error,
                    'code' => $this->resolveErrorCode(Error::Generic),
                ];
            }

            if ($error instanceof EnumError) {
                return $this->filterMetaKey([
                    'message' => $error->message(),
                    'code' => method_exists($error, 'code')
                        ? $error->code()
                        : $this->resolveErrorCode($error),
                    'meta' => method_exists($error, 'meta')
                        ? $error->meta()
                        : [],
                ]);
            }

            if ($error instanceof TransformableError) {
                return $this->filterMetaKey([
                    'message' => $error->message(),
                    'code' => $error->code(),
                    'meta' => $error->meta(),
                ]);
            }

            return $this->filterMetaKey(array_merge([
                'message' => Error::Unknown->message(),
                'code' => $this->resolveErrorCode(Error::Unknown),
                'meta' => [],
            ], Arr::only($error, ['message', 'code', 'meta'])));
        }, $errors);
    }

    protected function resolveErrorCode(EnumError $error): string
    {
        if (method_exists($error, 'code')) {
            return $error->code();
        }

        $code = class_basename($error);
        if ($error instanceof \UnitEnum) {
            return $code.':'.$error->name;
        }

        return $code;
    }

    protected function filterMetaKey(array $value): array
    {
        if (empty($value['meta'])) {
            unset($value['meta']);
        }

        return $value;
    }
}
