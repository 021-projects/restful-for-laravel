<?php

namespace O21\RestfulForLaravel\Tests\Unit;

use O21\RestfulForLaravel\ServiceProvider;
use O21\RestfulForLaravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_is_loaded(): void
    {
        $this->assertTrue(
            $this->app->getLoadedProviders()[ServiceProvider::class] ?? false
        );
    }

    public function test_helpers_file_is_loaded(): void
    {
        $this->assertTrue(function_exists('restful'));
    }
}
