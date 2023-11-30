<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
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
                Exporter::export($this->formatCollection($this->value), true),
                Exporter::export($this->formatCollection($other), true)
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
                class_exists(\Spatie\LaravelData\Data::class) && $value instanceof \Spatie\LaravelData\Data => new DataEquals($value),
                class_exists(\Spatie\LaravelData\DataCollection::class) && $value instanceof \Spatie\LaravelData\DataCollection => new DataCollectionEquals($value),
                is_object($value) => new IsEqual($value),
                default => new IsIdentical($value),
            };

            if (!$constraint->evaluate($other->get($key), returnResult: true)) {
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

        return Exporter::export($other, true) . ' is a collection';
    }

    public function toString(): string
    {
        return 'is same collection that ' . Exporter::export($this->value->toArray(), true);
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
