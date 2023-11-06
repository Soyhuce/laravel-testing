<?php declare(strict_types=1);

namespace Soyhuce\Testing\Mock;

use Closure;
use Mockery;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use Soyhuce\Testing\Match\Matcher;
use Soyhuce\Testing\Match\ValueMatcher;
use function Soyhuce\Testing\capture;

class PendingActionMock
{
    private Closure $returnsUsing;

    private readonly MockInterface $mock;

    private readonly ExpectationInterface $mockedMethod;

    /**
     * @param class-string $action
     */
    public function __construct(
        string $action,
    ) {
        $this->returnsUsing = fn () => null;

        $this->mock = Mockery::mock($action);
        $this->mockedMethod = $this->mock
            ->shouldReceive('execute')
            ->andReturnUsing($this->returnsUsing)
            ->once();

        app()->instance($action, $this->mock);
    }

    /**
     * @param array<int, mixed> $arguments
     */
    public function with(mixed ...$arguments): self
    {
        $matcher = Matcher::make(...array_map($this->toMatcher(...), $arguments));

        $this->mockedMethod->withArgs($matcher);

        return $this;
    }

    private function toMatcher(mixed $argument): mixed
    {
        if ($argument instanceof Closure || $argument instanceof ValueMatcher) {
            return $argument;
        }

        return Matcher::make($argument);
    }

    public function returns(Closure $returnsUsing): self
    {
        $this->returnsUsing = $returnsUsing;
        $this->mockedMethod->andReturnUsing($returnsUsing);

        return $this;
    }

    public function in(mixed &$in): self
    {
        $this->mockedMethod->andReturnUsing(capture($in, $this->returnsUsing));

        return $this;
    }

    public function neverCalled(): self
    {
        $this->mockedMethod->never();

        return $this;
    }

    public function anyTimes(): self
    {
        $this->mockedMethod->zeroOrMoreTimes();

        return $this;
    }
}
