<?php declare(strict_types=1);

namespace Soyhuce\Testing\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Soyhuce\Testing\Constraints\CollectionEquals;
use Soyhuce\Testing\Constraints\DataCollectionEquals;
use Soyhuce\Testing\Constraints\DataEquals;
use Soyhuce\Testing\Constraints\IsModel;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use function assert;
use function is_array;

trait LaravelAssertions
{
    public static function assertIsModel(Model $expected, mixed $actual, string $message = ''): void
    {
        $constraint = new IsModel($expected);

        Assert::assertThat($actual, $constraint, $message);
    }

    /**
     * @static
     * @param array<array-key, mixed>|Collection<array-key, mixed> $expected
     */
    public static function assertCollectionEquals(Collection|array $expected, mixed $actual, string $message = ''): void
    {
        if (is_array($expected)) {
            $expected = new Collection($expected);
        }

        $constraint = new CollectionEquals($expected);

        Assert::assertThat($actual, $constraint, $message);
    }

    /**
     * @static
     * @param array<array-key, mixed>|Collection<array-key, mixed> $expected
     */
    public static function assertCollectionEqualsCanonicalizing(
        Collection|array $expected,
        mixed $actual,
        string $message = '',
    ): void {
        if (is_array($expected)) {
            $expected = new Collection($expected);
        }

        $sort = fn (mixed $item) => $item instanceof Model ? $item->getKey() : $item;

        $expected = Arr::isList($expected->all()) ? $expected->sortBy($sort)->values() : $expected->sortBy($sort);

        if ($actual instanceof Collection) {
            $actual = Arr::isList($actual->all()) ? $actual->sortBy($sort)->values() : $actual->sortBy($sort);
        }

        $constraint = new CollectionEquals($expected);

        Assert::assertThat($actual, $constraint, $message);
    }

    public static function assertDataEquals(Data $expected, mixed $actual, string $message = ''): void
    {
        $constraint = new DataEquals($expected);

        Assert::assertThat($actual, $constraint, $message);
    }

    /**
     * @static
     * @param array<array-key, mixed>|DataCollection<array-key, mixed> $expected
     */
    public static function assertDataCollectionEquals(
        DataCollection|array $expected,
        mixed $actual,
        string $message = '',
    ): void {
        if (is_array($expected)) {
            assert($actual instanceof DataCollection, 'Failed asserting that value is a DataCollection');
            $expected = method_exists($actual->dataClass, 'collection')
                ? $actual->dataClass::collection($expected)
                : new DataCollection($actual->dataClass, $expected);
        }

        $constraint = new DataCollectionEquals($expected);

        Assert::assertThat($actual, $constraint, $message);
    }
}
