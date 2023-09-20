<?php declare(strict_types=1);

namespace Soyhuce\Testing\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Soyhuce\Testing\Constraints\CollectionEquals;
use Soyhuce\Testing\Constraints\DataEquals;
use Soyhuce\Testing\Constraints\IsModel;
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
     * @param array<array-key, mixed>|\Illuminate\Support\Collection<array-key, mixed> $expected
     */
    public static function assertCollectionEquals(Collection|array $expected, mixed $actual, string $message = ''): void
    {
        if (is_array($expected)) {
            $expected = new Collection($expected);
        }

        $constraint = new CollectionEquals($expected);

        Assert::assertThat($actual, $constraint, $message);
    }

    public static function assertDataEquals(\Spatie\LaravelData\Data $expected, mixed $actual, string $message = ''): void
    {
        $constraint = new DataEquals($expected);

        Assert::assertThat($actual, $constraint, $message);
    }
}
