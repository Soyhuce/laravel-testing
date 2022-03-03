<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsIdentical;
use SebastianBergmann\Comparator\ComparisonFailure;

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
                $this->exporter()->export($this->formatCollection($this->value)),
                $this->exporter()->export($this->formatCollection($other))
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
            $constraint = $value instanceof Model ? new IsModel($value) : new IsIdentical($value);

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

        return $this->exporter()->export($other) . ' is a collection';
    }

    public function toString(): string
    {
        return 'is same collection that ' . $this->exporter()->export($this->value->toArray());
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
