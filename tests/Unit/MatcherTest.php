<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Match\Matcher;
use Soyhuce\Testing\Tests\Fixtures\SimpleData;
use Soyhuce\Testing\Tests\TestCase;
use Spatie\LaravelData\DataCollection;

/**
 * @coversDefaultClass \Soyhuce\Testing\Match\Matcher
 */
class MatcherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::unguard();
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesArguments(): void
    {
        $result = Matcher::make('foo', 1, true)('foo', 1, true);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesCallbacks(): void
    {
        $result = Matcher::make(fn ($string) => $this->assertEquals('foo', $string))('foo');

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherReturnsFalseWhenCallback(): void
    {
        $result = Matcher::make(fn ($string) => false)('foo');
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherFailsWithError(): void
    {
        $this->expectException(ExpectationFailedException::class);
        Matcher::make('foo')('bar');
    }

    /**
     * @test
     * @covers ::isModel
     */
    public function matcherMatchesModelOrCollection(): void
    {
        $user = new User(['id' => 1]);
        $userClone = new User(['id' => 1]);
        $result = Matcher::make(
            $user,
            collect([$user, 'foo']),
            12
        )($userClone, collect([$userClone, 'foo']), 12);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::isModel
     */
    public function matcherMatchesModel(): void
    {
        $user = new User(['id' => 1]);
        $result = Matcher::isModel($user)($user);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::isModel
     */
    public function matcherFailsMatchingModel(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $user = new User(['id' => 1]);
        Matcher::isModel($user)(new User(['id' => 2]));
    }

    /**
     * @test
     * @covers ::collectionEquals
     */
    public function matcherMatchesCollection(): void
    {
        $collection = new Collection([1, 2]);
        $result = Matcher::collectionEquals([1, 2])($collection);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::collectionEquals
     */
    public function matcherFailsMatchingCollection(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $collection = new Collection([1, 2]);
        Matcher::collectionEquals([1, 2, 3])($collection);
    }

    /**
     * @test
     * @covers ::collectionEqualsCanonicalizing
     */
    public function matcherMatchesCollectionCanonicalizing(): void
    {
        $collection = new Collection([1, 2]);
        $result = Matcher::collectionEqualsCanonicalizing([2, 1])($collection);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::collectionEqualsCanonicalizing
     */
    public function matcherFailsMatchingCollectionCanonicalizing(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $collection = new Collection([1, 2]);
        Matcher::collectionEqualsCanonicalizing([2, 1, 3])($collection);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesData(): void
    {
        $expected = new SimpleData('foo', 1);
        $value = SimpleData::from(['name' => 'foo', 'age' => 1]);

        $result = Matcher::make($expected)($value);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherFailsMatchingData(): void
    {
        $expected = new SimpleData('foo', 1);
        $value = SimpleData::from(['name' => 'bar', 'age' => 12]);

        $this->expectException(ExpectationFailedException::class);
        Matcher::make($expected)($value);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesCollectionOfData(): void
    {
        $expected = new SimpleData('foo', 1);
        $value = SimpleData::from(['name' => 'foo', 'age' => 1]);

        $result = Matcher::make(collect([$expected]))(collect([$value]));

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesDataCollections(): void
    {
        $expected = new DataCollection(SimpleData::class, [
            ['name' => 'foo', 'age' => 1],
            ['name' => 'bar', 'age' => 14],
        ]);
        $value = new DataCollection(SimpleData::class, [
            new SimpleData('foo', 1),
            new SimpleData('bar', 14),
        ]);

        $result = Matcher::make($expected)($value);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherFailsToMatchDataCollections(): void
    {
        $expected = new DataCollection(SimpleData::class, [
            ['name' => 'foo', 'age' => 1],
            ['name' => 'bar', 'age' => 14],
        ]);
        $value = new DataCollection(SimpleData::class, [
            new SimpleData('bar', 14),
            new SimpleData('foo', 2),
        ]);

        $this->expectException(ExpectationFailedException::class);
        Matcher::make($expected)($value);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesObject(): void
    {
        $expected = new DateTimeImmutable('2021-01-01 12:00:00');
        $value = new DateTimeImmutable('2021-01-01 12:00:00');
        $result = Matcher::make($expected)($value);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::make
     */
    public function matcherMatchesArray(): void
    {
        $expected = [0, 'foo'];
        $value = [0, 'foo'];
        $result = Matcher::make($expected)($value);

        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::match
     */
    public function valueMatcherMatchesAttribute(): void
    {
        $user = new User(['id' => 1]);

        $result = Matcher::match(1, fn ($u) => $u->id)($user);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::match
     */
    public function valueMatcherFailsMatchingAttribute(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $user = new User(['id' => 1]);
        Matcher::match(2, fn ($u) => $u->id)($user);
    }

    /**
     * @test
     * @covers ::match
     */
    public function valueMatcherCanMatchMultipleArguments(): void
    {
        $user = new User(['id' => 1, 'name' => 'John']);

        $result = Matcher::match(1, fn ($u) => $u->id)->match('John', fn ($u) => $u->name)($user);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::match
     */
    public function valueMatcherCanFailMatchingMultipleArguments(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $user = new User(['id' => 1, 'name' => 'John']);

        Matcher::match(1, fn ($u) => $u->id)->match('Johny', fn ($u) => $u->name)($user);
    }

    /**
     * @test
     * @covers ::match
     */
    public function propertiesCanBeGivenAsNamedParameters(): void
    {
        $user = new User(['id' => 1, 'name' => 'John']);

        $result = Matcher::match(id: 1, name: 'John')($user);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::match
     */
    public function matcherCanBeConvertedToClosure(): void
    {
        $user = new User(['id' => 1, 'name' => 'John']);

        $result = Matcher::match(id: 1, name: 'John')->toClosure()($user);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::of
     */
    public function matcherMatchesClass(): void
    {
        $user = new User();

        $result = Matcher::of(User::class)($user);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @covers ::of
     */
    public function ofMatchesStrictClass(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $user = new User();

        Matcher::of(Model::class)($user);
    }
}
