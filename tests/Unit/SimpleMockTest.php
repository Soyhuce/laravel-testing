<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Tests\Fixtures\BaseClass;
use Soyhuce\Testing\Tests\Fixtures\BaseClassMock;
use Soyhuce\Testing\Tests\Fixtures\NoEnsuresWasCalledMock;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @coversNothing
 */
class SimpleMockTest extends TestCase
{
    /**
     * @test
     * @covers \SimpleMock::verify
     */
    public function itIsPossibleToMockAnAction(): void
    {
        BaseClassMock::setUp()
            ->calledWith(42);

        app(BaseClass::class)->execute(42);
    }

    /**
     * @test
     * @covers \SimpleMock::verify
     */
    public function parametersMustMatch(): void
    {
        BaseClassMock::setUp()
            ->calledWith(42);

        $this->expectException(ExpectationFailedException::class);
        app(BaseClass::class)->execute(0);
    }

    /**
     * @test
     * @covers \SimpleMock::verify
     */
    public function argumentsCanBeMatchedWithClosure(): void
    {
        BaseClassMock::setUp()
            ->calledWith(fn (int $value) => $value === 42);

        app(BaseClass::class)->execute(42);
    }

    /**
     * @test
     * @covers \SimpleMock::verifyAndReturns
     */
    public function returnValueCanBeDefined(): void
    {
        BaseClassMock::setUp()
            ->calledWith('foo')
            ->returns('bar');

        $value = app(BaseClass::class)->executeAndReturns('foo');
        $this->assertEquals('bar', $value);
    }

    /**
     * @test
     * @covers \SimpleMock::verifyAndReturns
     */
    public function calledWithValueCanBeSkipped(): void
    {
        BaseClassMock::setUp()
            ->returns('bar');

        $value = app(BaseClass::class)->executeAndReturns('foo');
        $this->assertEquals('bar', $value);
    }

    /**
     * @test
     * @covers \SimpleMock::verifyAndReturns
     */
    public function verifyAndReturnVerifiesInput(): void
    {
        BaseClassMock::setUp()
            ->calledWith('foo')
            ->returns('bar');

        $this->expectException(ExpectationFailedException::class);
        app(BaseClass::class)->executeAndReturns('toto');
    }

    /**
     * @test
     * @covers \SimpleMock::verifyAndReturns
     */
    public function mockMustBeCalled(): void
    {
        BaseClassMock::setUp()
            ->calledWith('foo')
            ->returns('bar');

        $this->expectException(ExpectationFailedException::class);

        $this->tearDown();
    }

    /**
     * @test
     * @covers \SimpleMock::verifyAndReturns
     */
    public function mockDoesNotAlwaysHaveToBeCalled(): void
    {
        NoEnsuresWasCalledMock::setUp()
            ->calledWith('foo')
            ->returns('bar');

        $this->assertTrue(true);
    }
}
