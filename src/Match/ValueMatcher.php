<?php declare(strict_types=1);

namespace Soyhuce\Testing\Match;

use Closure;

class ValueMatcher
{
    /** @var array<array{mixed, callable}> */
    protected array $expectations = [];

    public function match(mixed $expected, callable $transformation): self
    {
        $this->expectations[] = [$expected, $transformation];

        return $this;
    }

    public function properties(mixed ...$args): self
    {
        foreach ($args as $key => $value) {
            $this->match($value, fn ($item) => $item->{$key});
        }

        return $this;
    }

    public function toClosure(): Closure
    {
        return Closure::fromCallable($this);
    }

    public function __invoke(mixed $value): bool
    {
        foreach ($this->expectations as [$expected, $transformation]) {
            $result = Matcher::make($expected)($transformation($value));
            if ($result === false) {
                return false;
            }
        }

        return true;
    }
}
