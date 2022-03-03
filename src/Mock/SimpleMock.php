<?php declare(strict_types=1);

namespace Soyhuce\Testing\Mock;

use PHPUnit\Framework\Assert;
use function array_slice;
use function count;
use function is_callable;

trait SimpleMock
{
    /** @var array<mixed>|callable|null */
    protected $expectedArguments;

    protected mixed $returnValue;

    private bool $called = false;

    public static function setUp(): self
    {
        $instance = static::newInstance();
        app()->singleton(
            static::abstract(),
            static fn () => $instance
        );

        return $instance;
    }

    abstract protected static function abstract(): string;

    protected static function newInstance(): self
    {
        return app()->build(static::class);
    }

    public function calledWith(mixed ...$args): self
    {
        $this->expectedArguments = is_callable($args[0]) ? $args[0] : $args;

        return $this;
    }

    public function returns(mixed $value): self
    {
        $this->returnValue = $value;

        return $this;
    }

    protected function verify(mixed ...$args): void
    {
        $this->isCalled();
        if ($this->expectedArguments === null) {
            return;
        }

        if (is_callable($this->expectedArguments)) {
            Assert::assertTrue(($this->expectedArguments)(...$args));
        } else {
            Assert::assertEquals($this->expectedArguments, array_slice($args, 0, count($this->expectedArguments)));
        }
    }

    protected function verifyAndReturns(mixed ...$args): mixed
    {
        $this->verify(...$args);

        return $this->returnValue;
    }

    protected function isCalled(): void
    {
        $this->called = true;
    }

    protected function wasCalled(): bool
    {
        return $this->called;
    }

    protected function ensuresWasCalled(): bool
    {
        return property_exists($this, 'ensuresWasCalled') ? $this->ensuresWasCalled : true;
    }

    public function __destruct()
    {
        if (!$this->ensuresWasCalled()) {
            return;
        }

        Assert::assertTrue($this->wasCalled(), 'Fail to assert that the mock ' . static::class . ' was called');
    }
}
