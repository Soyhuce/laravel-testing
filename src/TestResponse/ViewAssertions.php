<?php declare(strict_types=1);

namespace Soyhuce\Testing\TestResponse;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @mixin \Illuminate\Testing\TestResponse
 */
class ViewAssertions
{
    public function assertViewHasNull(): Closure
    {
        return function (string $key): self {
            $this->assertViewHas($key);

            PHPUnit::assertNull(Arr::get($this->original->gatherData(), $key));

            return $this;
        };
    }
}
