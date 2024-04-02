<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;
use function is_array;
use function is_object;

class CollectionEquals extends Constraint
{
    /**
     * @param \Illuminate\Support\Collection<array-key, mixed> $value
     */
    public function __construct(
        protected Collection $value,
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

        $failure = $other instanceof Collection
            ? new ComparisonFailure(
                $this->value,
                $other,
                (new Exporter())->export($this->formatCollection($this->value)),
                (new Exporter())->export($this->formatCollection($other))
            )
            : null;

        $this->fail($other, $description, $failure);
    }

    protected function matches(mixed $other): bool
    {
        if (!$other instanceof Collection) {
            return false;
        }

        if (array_keys($this->value->all()) !== array_keys($other->all())) {
            return false;
        }

        foreach ($this->value as $key => $value) {
            $constraint = match (true) {
                $value instanceof Model => new IsModel($value),
                $value instanceof Collection => new self($value),
                class_exists(\Spatie\LaravelData\Data::class) && $value instanceof \Spatie\LaravelData\Data => new DataEquals($value),
                class_exists(\Spatie\LaravelData\DataCollection::class) && $value instanceof \Spatie\LaravelData\DataCollection => new DataCollectionEquals($value),
                is_object($value) => new IsEqual($value),
                is_array($value) => new self(Collection::make($value)),
                default => new IsIdentical($value),
            };

            $otherValue = match (true) {
                is_array($value) => Collection::make($other->get($key)),
                default => $other->get($key),
            };

            if (!$constraint->evaluate($otherValue, returnResult: true)) {
                return false;
            }
        }

        return true;
    }

    protected function failureDescription(mixed $other): string
    {
        if ($other instanceof Collection) {
            return 'the two collections are equal';
        }

        return (new Exporter())->export($other) . ' is a collection';
    }

    public function toString(): string
    {
        return 'is same collection that ' . (new Exporter())->export($this->value->toArray());
    }

    /**
     * @param \Illuminate\Support\Collection<array-key, mixed> $value
     * @return \Illuminate\Support\Collection<array-key, mixed>
     */
    private function formatCollection(Collection $value): Collection
    {
        return $value->map(
            fn (mixed $item) => $item instanceof Model ? IsModel::export($item) : $item
        );
    }
}
