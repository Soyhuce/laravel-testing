<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\Testing\TestingServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDeprecationHandling();
    }

    protected function getPackageProviders($app)
    {
        return [
            TestingServiceProvider::class,
        ];
    }
}
