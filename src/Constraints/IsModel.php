<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;
use function sprintf;

class IsModel extends Constraint
{
    public function __construct(
        protected Model $value,
    ) {
    }

    public function evaluate(mixed $other, string $description = '', bool $returnResult = false): ?bool
    {
        $success = $other instanceof Model && $other->is($this->value);

        if ($returnResult) {
            return $success;
        }

        if ($success) {
            return null;
        }

        $failure = $other instanceof Model
            ? new ComparisonFailure(
                $this->value,
                $other,
                (new Exporter())->export(self::export($this->value)),
                (new Exporter())->export(self::export($other))
            )
            : null;

        $this->fail($other, $description, $failure);
    }

    protected function failureDescription(mixed $other): string
    {
        if ($other instanceof Model) {
            return $this->shortDescription($other) . ' ' . $this->toString();
        }

        return (new Exporter())->export($other) . ' ' . $this->toString();
    }

    public function toString(): string
    {
        return 'is same model that ' . $this->shortDescription($this->value);
    }

    private function shortDescription(Model $model): string
    {
        return sprintf(
            '%s with %s %s',
            $model::class,
            $model->getKeyName(),
            $model->getKey()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function export(Model $model): array
    {
        $attributes = $model->getAttributes();

        ksort($attributes);

        return $attributes;
    }
}
