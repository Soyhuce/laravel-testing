<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Support\Str;
use Soyhuce\Testing\Tests\TestCase;
use function Soyhuce\Testing\capture;

/**
 * @coversNothing
 */
class UtilsTest extends TestCase
{
    /**
     * @test
     * @covers \Soyhuce\Testing\capture
     */
    public function captureCapturesReturnValue(): void
    {
        $callback = capture(
            $result,
            fn () => Str::upper('foo')
        );

        $this->assertNull($result);

        $value = $callback();
        $this->assertEquals('FOO', $value);

        $this->assertEquals('FOO', $result);
    }
}
