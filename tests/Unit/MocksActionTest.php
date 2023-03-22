<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Mockery;
use Mockery\Exception\InvalidCountException;
use PHPUnit\Framework\AssertionFailedError;
use Soyhuce\Testing\Concerns\MocksActions;
use Soyhuce\Testing\Tests\Fixtures\BasicAction;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @covers \Soyhuce\Testing\Concerns\MocksActions
 */
class MocksActionTest extends TestCase
{
    use MocksActions;

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function theActionFailsIfNotCalled(): void
    {
        $this->mockAction(BasicAction::class)
            ->with(2)
            ->returns(fn () => 4)
            ->in($result);

        $this->expectException(InvalidCountException::class);

        Mockery::close();
    }

    /**
     * @test
     */
    public function theActionFailsIfCalledWithOtherArgument(): void
    {
        $this->mockAction(BasicAction::class)
            ->with(2)
            ->anyTimes();

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Failed asserting that 3 is identical to 2.');

        app(BasicAction::class)->execute(3);
    }

    /**
     * @test
     */
    public function theActionCanBeNeverCalled(): void
    {
        $this->mockAction(BasicAction::class)
            ->neverCalled();

        $this->assertTrue(true);
    }

    /**
     * @test
     */
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
