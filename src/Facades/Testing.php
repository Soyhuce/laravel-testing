<?php declare(strict_types=1);

namespace Soyhuce\Testing\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Soyhuce\Testing\Testing
 */
class Testing extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-testing';
    }
}
