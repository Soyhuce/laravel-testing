<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

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

    public function sameCollection(): array
    {
        Model::unguard();

        return [
            [new Collection([1, 2, 3]), new Collection([1, 2, 3])],
            [[1, 2, 3], new Collection([1, 2, 3])],
            [new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'c' => 3])],
            [['a' => 1, 'b' => 2, 'c' => 3], new Collection(['a' => 1, 'b' => 2, 'c' => 3])],
            [new Collection([new User(['id' => 1, 'name' => 'John'])]),  new Collection([new User(['id' => 1, 'name' => 'Peter'])])],
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

    public function differentCollections(): array
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
