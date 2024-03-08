<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\LaravelAssertions;
use Soyhuce\Testing\Tests\Fixtures\SimpleData;
use Soyhuce\Testing\Tests\TestCase;
use Spatie\LaravelData\DataCollection;

/**
 * @covers \Soyhuce\Testing\Constraints\DataCollectionEquals
 */
class DataCollectionEqualsTest extends TestCase
{
    use LaravelAssertions;

    public static function sameDataCollection(): array
    {
        return [
            [
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                fn () => [['name' => 'John', 'age' => 42], ['name' => 'Peter', 'age' => 25]],
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                fn () => ['a' => ['name' => 'John', 'age' => 42], 'b' => ['name' => 'Peter', 'age' => 25]],
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider sameDataCollection
     */
    public function dataCollectionsAreEqual(mixed $expected, mixed $actual): void
    {
        $this->assertDataCollectionEquals($expected(), $actual());
    }

    public static function differentDataCollections(): array
    {
        return [
            [
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42)]),
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, [new SimpleData(name: 'Peter', age: 25), new SimpleData(name: 'John', age: 42)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'George', age: 37)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'c' => new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, ['b' => new SimpleData(name: 'Peter', age: 25), 'a' => new SimpleData(name: 'John', age: 42)]),
            ],
            [
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                fn () => new DataCollection(SimpleData::class, ['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25), new SimpleData(name: 'George', age: 37)]),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider differentDataCollections
     */
    public function dataCollectionsAreDifferent(mixed $expected, mixed $actual): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->assertDataCollectionEquals($expected(), $actual());
    }
}
