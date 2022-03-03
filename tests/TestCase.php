<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\Testing\TestingServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TestingServiceProvider::class,
        ];
    }
}
