<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Mockery;
use Mockery\Exception\InvalidCountException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\Testing\Concerns\MocksActions;
use Soyhuce\Testing\Tests\Fixtures\BasicAction;
use Soyhuce\Testing\Tests\TestCase;

#[CoversClass(MocksActions::class)]
class MocksActionTest extends TestCase
{
    use MocksActions;

    #[Test]
    public function theActionCanBeMocked(): void
    {
        $this->mockAction(BasicAction::class)
            ->with(2)
            ->returns(fn () => 4)
            ->in($result);

        $value = app(BasicAction::class)->execute(2);

        $this->assertEquals(4, $value);
        $this->assertEquals(4, $result);
    }

    #[Test]
    public function receivesTheArgumentInReturnsCallback(): void
    {
        $this->mockAction(BasicAction::class)
            ->with(2)
            ->returns(fn (int $value) => $value + 3)
            ->in($result);

        $value = app(BasicAction::class)->execute(2);

        $this->assertEquals(5, $value);
        $this->assertEquals(5, $result);
    }

    #[Test]
    public function theActionFailsIfNotCalled(): void
    {
        $this->mockAction(BasicAction::class)
            ->with(2)
            ->returns(fn () => 4)
            ->in($result);

        $this->expectException(InvalidCountException::class);

        Mockery::close();
    }

    #[Test]
    public function theActionFailsIfCalledWithOtherArgument(): void
    {
        $this->mockAction(BasicAction::class)
            ->with(2)
            ->anyTimes();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Failed asserting that 3 is identical to 2.');

        app(BasicAction::class)->execute(3);
    }

    #[Test]
    public function theActionCanBeNeverCalled(): void
    {
        $this->mockAction(BasicAction::class)
            ->neverCalled();

        $this->assertTrue(true);
    }

    #[Test]
    public function theActionFailsIfCalledButDeclaredNeverCalled(): void
    {
        $this->mockAction(BasicAction::class)
            ->returns(fn () => 4)
            ->neverCalled();

        app(BasicAction::class)->execute(2);

        $this->expectException(InvalidCountException::class);

        Mockery::close();
    }
}
