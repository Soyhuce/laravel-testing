<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;
use Spatie\LaravelData\DataCollection;

class DataCollectionEquals extends Constraint
{
    /**
     * @param DataCollection<array-key, mixed> $value
     */
    public function __construct(
        protected DataCollection $value,
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

        $failure = $other instanceof DataCollection
            ? new ComparisonFailure(
                $this->value,
                $other,
                (new Exporter())->export($this->value),
                (new Exporter())->export($other)
            )
            : null;

        $this->fail($other, $description, $failure);
    }

    protected function matches(mixed $other): bool
    {
        if (!$other instanceof DataCollection) {
            return false;
        }

        if ($other::class !== $this->value::class) {
            return false;
        }

        if ($this->value->dataClass !== $other->dataClass) {
            return false;
        }

        if ($this->value->toCollection()->keys()->all() !== $other->toCollection()->keys()->all()) {
            return false;
        }

        foreach ($this->value as $key => $value) {
            if (!(new DataEquals($value))->evaluate($other->offsetGet($key), returnResult: true)) {
                return false;
            }
        }

        return true;
    }

    protected function failureDescription(mixed $other): string
    {
        if ($other instanceof DataCollection) {
            return 'the two data collections are equal';
        }

        return (new Exporter())->export($other) . ' is a collection';
    }

    public function toString(): string
    {
        return 'is same collection that ' . (new Exporter())->export($this->value->toArray());
    }
}
