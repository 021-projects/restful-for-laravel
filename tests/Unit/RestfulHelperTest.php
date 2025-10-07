<?php

namespace O21\RestfulForLaravel\Tests\Unit;

use Illuminate\Http\JsonResponse;
use O21\RestfulForLaravel\Error;
use Orchestra\Testbench\TestCase;

class RestfulHelperTest extends TestCase
{
    public function test_success_response(): void
    {
        $response = restful(['foo' => 'bar']);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['foo' => 'bar'], $response->getData(true));
    }

    public function test_error_response(): void
    {
        $response = restful(errors: ['Error']);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->getData(true));

        $firstError = $response->getData(true)['errors'][0];
        $this->assertEquals('Error', $firstError['message']);
        $this->assertEquals('Error:Generic', $firstError['code']);

        $response = restful(errors: [
            new Error('TestCode', 'Custom error message', [
                'field' => 'email',
            ]),
        ], status: 422);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->getData(true));
        $firstError = $response->getData(true)['errors'][0];
        $this->assertEquals('Custom error message', $firstError['message']);
        $this->assertEquals('TestCode', $firstError['code']);
        $this->assertEquals(['field' => 'email'], $firstError['meta']);
    }

    public function test_closure_success(): void
    {
        $response = restful(fn () => ['result' => 123]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['result' => 123], $response->getData(true));
    }

    public function test_closure_exception_with_fallback(): void
    {
        $response = restful(
            fn () => throw new \Exception('fail'),
            fallback: function ($e, $toErrors) {
                return $toErrors(['fallback error'], 500);
            }
        );
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->getData(true));
    }
}
