<?php declare(strict_types=1);

namespace Soyhuce\Testing\Constraints;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Transformation\TransformationContextFactory;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;

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
                (new Exporter())->export(self::export($this->value)),
                (new Exporter())->export(self::export($other))
            )
            : null;

        $this->fail($other, $description, $failure);
    }

    protected function matches(mixed $other): bool
    {
        if (!is_a($other, $this->value::class)) {
            return false;
        }

        return (new CollectionEquals(Collection::make(self::export($this->value))))
            ->matches(Collection::make(self::export($other)));
    }

    protected function failureDescription(mixed $other): string
    {
        if ($other instanceof Data) {
            return 'given ' . $other::class . ' ' . $this->toString();
        }

        return (new Exporter())->export($other) . ' ' . $this->toString();
    }

    public function toString(): string
    {
        return 'is same data that expected ' . $this->value::class;
    }

    /**
     * @return array<string, mixed>
     */
    public static function export(Data $data): array
    {
        if (class_exists(TransformationContextFactory::class)) {
            return $data->transform(
                TransformationContextFactory::create()
                    ->withValueTransformation(false)
                    ->withWrapExecutionType(WrapExecutionType::Disabled)
                    ->withoutPropertyNameMapping()
            );
        }

        return $data->transform(
            transformValues: false,
            wrapExecutionType: WrapExecutionType::Disabled,
            mapPropertyNames: false
        );
    }
}
