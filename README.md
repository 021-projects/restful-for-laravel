# Restful for Laravel

## What problem does this package solve?
This package provides a simple and elegant way to handle RESTful responses in Laravel applications. 
It allows you to define your data fetching logic and error handling in a clean and concise manner, reducing boilerplate code and improving readability.
Restful for Laravel also supports exception handling: it consolidates errors into a single common form.

## Requirements
- PHP 8.1 or higher

## Installation
You can install the package via Composer:
```bash
composer require genesis/restful-for-laravel
```

## Response Structure

### Success
You can pass to data any value that can be passed to `response()->json()`

### Error
```json
{
  "errors": [
    {
      "code": "Error:Generic",
      "message": "Error message",
      /* optional */
      "meta": {
        /* any additional data here */
      }
    }
    /* more errors can be here */
  ]
}
```

## Usage

```php
use App\Http\Controllers\Controller;
use App\Repositories\FooRepository;
use App\Enums\Errors\BarError;
use O21\RestfulForLaravel\Contracts\EnumError;
use Illuminate\Http\JsonResponse;

enum BarError implements EnumError
{
    case FailedToFetchBars;

    public function message(): string
    {
        return match ($this) {
            self::FailedToFetchBars => 'Failed to fetch bars from external resource.',
        };
    }
}

class FooBarController extends Controller
{
    public function bar(): JsonResponse
    {
        return restful(
            data: fn (FooRepository $repo) => $repo->fetchBarsFromExternalResource(),
            fallback: fn (callable $toErrors) => $toErrors(
                errors: [
                    Bar::FailedToFetchBars,
                ],
                status: 503,
            ),
        );
    }
}
```

Just to compare, without this package, your `bar()` method would look like this:
```php
public function bar(FooRepository $repo): JsonResponse
{
    try {
        return response()->json($repo->fetchBarsFromExternalResource());
    } catch (\Exception $e) {
        report($e);
        
        return response()->json([
            'errors' => [
                [
                    'code' => 'failed_to_fetch_bars',
                    'message' => 'Failed to fetch bars from external resource.',
                ],
            ],
        ], 503);
    }
}
```

More examples can be found in the [examples](examples) directory.

## Need development services?

For expert assistance with developing or integrating any type of website or service, including financial applications, feel free to reach out to us at [studio.gnz.is](https://studio.gnz.is). We're here to help!

