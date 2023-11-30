<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\LaravelAssertions;
use Soyhuce\Testing\Tests\Fixtures\SimpleData;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @covers \Soyhuce\Testing\Constraints\DataCollectionEquals
 */
class DataCollectionEqualsTest extends TestCase
{
    use LaravelAssertions;

    public static function sameDataCollection(): array
    {
        Model::unguard();

        return [
            [
                SimpleData::collection([new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection([new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                [['name' => 'John', 'age' => 42], ['name' => 'Peter', 'age' => 25]],
                SimpleData::collection([new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                ['a' => ['name' => 'John', 'age' => 42], 'b' => ['name' => 'Peter', 'age' => 25]],
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider sameDataCollection
     */
    public function dataCollectionsAreEqual(mixed $expected, mixed $actual): void
    {
        $this->assertDataCollectionEquals($expected, $actual);
    }

    public static function differentDataCollections(): array
    {
        Model::unguard();

        return [
            [
                SimpleData::collection([new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection([new SimpleData(name: 'John', age: 42)]),
            ],
            [
                SimpleData::collection([new SimpleData(name: 'John', age: 42)]),
                SimpleData::collection([new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                SimpleData::collection([new SimpleData(name: 'John', age: 42), new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection([new SimpleData(name: 'Peter', age: 25), new SimpleData(name: 'John', age: 42)]),
            ],
            [
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42)]),
            ],
            [
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'George', age: 37)]),
            ],
            [
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'c' => new SimpleData(name: 'Peter', age: 25)]),
            ],
            [
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection(['b' => new SimpleData(name: 'Peter', age: 25), 'a' => new SimpleData(name: 'John', age: 42)]),
            ],
            [
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25)]),
                SimpleData::collection(['a' => new SimpleData(name: 'John', age: 42), 'b' => new SimpleData(name: 'Peter', age: 25), new SimpleData(name: 'George', age: 37)]),
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

        $this->assertDataCollectionEquals($expected, $actual);
    }
}
