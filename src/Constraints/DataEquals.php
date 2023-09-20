<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use Spatie\LaravelData\Data;

class DataEquals extends Constraint
{
    public function __construct(
        protected Data $value,
    ) {
    }

    public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
    {
        $success = $this->matches($other);

        if ($returnResult) {
            return $success;
        }

        if ($success) {
            return null;
        }

        $failure = $other instanceof Data
            ? new ComparisonFailure(
                $this->value,
                $other,
                $this->exporter()->export($this->value),
                $this->exporter()->export($other)
            )
            : null;

        $this->fail($other, $description, $failure);
    }

    protected function matches(mixed $other): bool
    {
        if (!$other instanceof Data) {
            return false;
        }

        return (new CollectionEquals(Collection::make($this->value->all())))
            ->matches(Collection::make($other->all()));
    }

    protected function failureDescription(mixed $other): string
    {
        return $this->exporter()->export($other) . ' ' . $this->toString();
    }

    public function toString(): string
    {
        return 'is same data that ' . $this->exporter()->export($this->value);
    }
}
