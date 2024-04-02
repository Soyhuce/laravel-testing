<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\LaravelAssertions;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @covers \Soyhuce\Testing\Constraints\CollectionEquals
 */
class CollectionEqualsTest extends TestCase
{
    use LaravelAssertions;

    public static function sameCollection(): array
    {
        Model::unguard();

        return [
            [new Collection([1, 2, 3]), new Collection([1, 2, 3])],
            [[1, 2, 3], new Collection([1, 2, 3])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'c' => 3])],
            [['a' => 1, 'b' => 2, 'c' => 3], new Collection(['a' => 1, 'b' => 2, 'c' => 3])],
            [new Collection([new User(['id' => 1, 'name' => 'John'])]),  new Collection([new User(['id' => 1, 'name' => 'Peter'])])],
            [
                new Collection([CarbonImmutable::create(2022, 5, 11)]),
                new Collection([CarbonImmutable::createFromFormat('!Y-m-d', '2022-05-11')]),
            ],
            [
                new Collection([new Collection([1, 2, 3])]),
                new Collection([new Collection([1, 2, 3])]),
            ],
            [
                new Collection([[1, 2, 3]]),
                new Collection([[1, 2, 3]]),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider sameCollection
     */
    public function collectionsAreEqual(mixed $first, mixed $second): void
    {
        $this->assertCollectionEquals($first, $second);
    }

    public static function differentCollections(): array
    {
        Model::unguard();

        return [
            [new Collection([1, 2]), new Collection([1, 2, 3])],
            [new Collection([1, 2, 3]), new Collection([1, 2])],
            [new Collection([1, 2, 3]), new Collection([3, 1, 2])],
            [new Collection([1, 2, 3]), new Collection([3, 1, 2])],
            [new Collection([1, 2, 3]), new Collection([1, 2, '3'])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'c' => 4])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'd' => 3])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'c' => 3, 'b' => 2])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 3])],
            [new Collection([new User(['id' => 1, 'name' => 'John'])]),  new Collection([new User(['id' => 2, 'name' => 'Peter'])])],
            [
                new Collection([new Collection([1, 2, 3])]),
                new Collection([new Collection([1, '2', 3])]),
            ],
            [
                new Collection([[1, 2, 3]]),
                new Collection([[1, '2', 3]]),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider differentCollections
     */
    public function collectionsAreDifferent(mixed $first, mixed $second): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->assertCollectionEquals($first, $second);
    }
}
