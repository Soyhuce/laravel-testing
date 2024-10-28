<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @coversNothing
 */
class DataAssertionsTest extends TestCase
{
    /**
     * @test
     * @covers \Soyhuce\Testing\TestResponse\DataAssertions::assertData
     */
    public function assertDataIsSuccessfulWhenDataMatches(): void
    {
        $response = new TestResponse(new Response([
            'data' => [
                'foo' => 'bar',
                'titi' => 'tata',
            ],
        ]));

        $response->assertData([
            'foo' => 'bar',
            'titi' => 'tata',
        ]);
    }

    public static function badAssertDataData()
    {
        return [
            [
                ['foo' => 'bar'], ['foo' => 'baz'],
            ],
            [
                ['foo' => 1], ['foo' => '1'],
            ],
            [
                ['foo', 'bar'], ['bar', 'foo'],
            ],
            [
                ['foo' => 'bar', 'toto' => 'titi'],
                ['toto' => 'titi', 'foo' => 'bar'],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertData
     * @dataProvider badAssertDataData
     */
    public function assertDataIsNotSuccessfulWhenDataDoesNotMatch(array $expected, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertData($expected);
    }

    public static function goodAssertDataPathData()
    {
        return [
            [
                'foo', 'bar', ['foo' => 'bar', 'titi' => 'tata'],
            ],
            [
                '*.id', [1, 2], [['id' => 1], ['id' => 2]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPath
     * @dataProvider goodAssertDataPathData
     */
    public function assertDataPathIsSuccessfulWhenDataMatches(string $path, mixed $expected, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $response->assertDataPath($path, $expected);
    }

    public static function badAssertDataPathData()
    {
        return [
            [
                'foo', 'bar', ['foo' => 'baz'],
            ],
            [
                'key', ['foo', 'bar'], ['key' => ['bar', 'foo']],
            ],
            [
                'key',
                ['foo' => 'bar', 'toto' => 'titi'],
                ['key' => ['toto' => 'titi', 'foo' => 'bar']],
            ],
            [
                'key', 'foo', ['foo' => 'bar'],
            ],
            [
                '*.id', [1, 2], [['id' => 1]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPath
     * @dataProvider badAssertDataPathData
     */
    public function assertDataPathIsNotSuccessfulWhenDataDoesNotMatch(string $path, mixed $expected, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertDataPath($path, $expected);
    }

    public static function goodAssertDataPathCanonicalizingData()
    {
        return [
            [
                'key', ['foo', 'bar'], ['key' => ['bar', 'foo']],
            ],
            [
                'key', ['baz' => 'foz', 'foo' => 'bar'], ['key' => ['foo' => 'bar', 'baz' => 'foz']],
            ],
            [
                '*.id', [1, 2], [['id' => 1], ['id' => 2]],
            ],
            [
                '*.id', [2, 1], [['id' => 1], ['id' => 2]],
            ],
            [
                '*.id', [1, 2], [['id' => 2], ['id' => 1]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPathCanonicalizing
     * @dataProvider goodAssertDataPathCanonicalizingData
     */
    public function assertDataPathCanonicalizingIsSuccessfulWhenDataMatches(
        string $path,
        array $expected,
        array $actual,
    ): void {
        $response = new TestResponse(new Response(['data' => $actual]));

        $response->assertDataPathCanonicalizing($path, $expected);
    }

    public static function badAssertDataPathCanonicalizingData()
    {
        return [
            [
                'key', ['baz' => 'foz', 'foo' => 'bar'], ['key' => ['bar' => 'foo', 'foz' => 'baz']],
            ],
            [
                '*.id', [1, 2], [['id' => 1]],
            ],
            [
                '*.id', [1, 2], [['id' => 1], ['id' => 2], ['id' => 3]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPathCanonicalizing
     * @dataProvider badAssertDataPathCanonicalizingData
     */
    public function assertDataPathCanonicalizingIsNotSuccessfulWhenDataDoesNotMatch(string $path, mixed $expected, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertDataPathCanonicalizing($path, $expected);
    }

    /**
     * @test
     * @covers \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPaths
     */
    public function assertDataPathsIsSuccessful(): void
    {
        $response = new TestResponse(new Response(['data' => ['foo' => 'bar', 'titi' => ['toto' => 'tata']]]));

        $response->assertDataPaths(['foo' => 'bar', 'titi.toto' => 'tata']);
    }

    public static function goodAssertDataMissingData()
    {
        return [
            [
                1, [2, 3, 4],
            ],
            [
                1, ['1', '2'],
            ],
            [
                ['foo' => 'bar'], [['foo' => 'toto'], ['foo' => 'baz']],
            ],
        ];
    }

    /**
     * @test
     * @covers \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPathsCanonicalizing
     */
    public function assertDataPathsCanonicalizingIsSuccessful(): void
    {
        $data = [
            'data' => [
                'foo' => ['baz', 'boz', 'bar'],
                'bar' => [
                    'baz' => 'faz',
                    'boz' => 'foz',
                ],
                'key' => [
                    ['foo' => 'baz'],
                    ['foo' => 'boz'],
                    ['foo' => 'bar'],
                ],
                'baz' => [
                    ['id' => 1],
                    ['id' => 2],
                    ['id' => 3],
                ],
                'boz' => [1, 2, 3],
            ],
        ];

        $response = new TestResponse(new Response($data));

        $response->assertDataPathsCanonicalizing([
            'foo' => ['bar', 'baz', 'boz'],
            'bar' => ['boz' => 'foz', 'baz' => 'faz'],
            'key.*.foo' => ['bar', 'baz', 'boz'],
            'baz.*.id' => [3, 1, 2],
            'boz' => [2, 3, 1],
        ]);
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataMissing
     * @dataProvider goodAssertDataMissingData
     */
    public function assertDataMissingIsSuccessfulWhenItemIsNotInResponse(mixed $item, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $response->assertDataMissing($item);
    }

    public static function badAssertDataMissingData()
    {
        return [
            [
                1, [1, 2, 3, 4],
            ],
            [
                ['foo' => 'bar'], [['foo' => 'toto'], ['foo' => 'bar']],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPath
     * @dataProvider badAssertDataMissingData
     */
    public function assertDataMissingIsNotSuccessfulWhenDataContainsTheItem(mixed $item, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertDataMissing($item);
    }

    public static function goodAssertDataPathMissingData()
    {
        return [
            [
                'key', 1, ['key' => [2, 3, 4]],
            ],
            [
                'key', 1, ['key' => ['1', '2']],
            ],
            [
                'key', ['foo' => 'bar'], ['key' => [['foo' => 'toto'], ['foo' => 'baz']]],
            ],
            [
                '*.id', [1], [['id' => 2], ['id' => 3]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPathMissing
     * @dataProvider goodAssertDataPathMissingData
     */
    public function assertDataPathMissingIsSuccessfulWhenItemIsNotInResponse(string $key, mixed $item, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $response->assertDataPathMissing($key, $item);
    }

    public static function badAssertDataPathMissingData()
    {
        return [
            [
                'key', 1, ['key' => [1, 2, 3, 4]],
            ],
            [
                'key', ['foo' => 'bar'], ['key' => [['foo' => 'toto'], ['foo' => 'bar']]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\DataAssertions::assertDataPathMissing
     * @dataProvider badAssertDataPathMissingData
     */
    public function assertDataPathMissingIsNotSuccessfulWhenDataContainsTheItem(string $key, mixed $item, array $actual): void
    {
        $response = new TestResponse(new Response(['data' => $actual]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertDataPathMissing($key, $item);
    }
}
