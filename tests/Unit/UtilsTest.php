<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\Testing\Tests\TestCase;
use function Soyhuce\Testing\capture;

#[CoversNothing]
class UtilsTest extends TestCase
{
    #[Test]
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
