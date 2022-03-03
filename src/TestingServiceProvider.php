<?php declare(strict_types=1);

namespace Soyhuce\Testing;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use Soyhuce\Testing\TestResponse\ContractAssertions;
use Soyhuce\Testing\TestResponse\DataAssertions;
use Soyhuce\Testing\TestResponse\JsonAssertions;
use Soyhuce\Testing\TestResponse\ViewAssertions;

class TestingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        TestResponse::mixin(new ContractAssertions());
        TestResponse::mixin(new DataAssertions());
        TestResponse::mixin(new JsonAssertions());
        TestResponse::mixin(new ViewAssertions());
    }
}
