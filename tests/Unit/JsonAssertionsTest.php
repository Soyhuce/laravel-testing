<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @coversNothing
 */
class JsonAssertionsTest extends TestCase
{
    public static function goodAssertJsonPathMissingData()
    {
        return [
            [
                'key',
                1,
                ['key' => [2, 3, 4]],
            ],
            [
                'key',
                1,
                ['key' => ['1', '2']],
            ],
            [
                'key',
                ['foo' => 'bar'],
                ['key' => [['foo' => 'toto'], ['foo' => 'baz']]],
            ],
            [
                '*.id',
                [1],
                [['id' => 2], ['id' => 3]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\JsonAssertions::assertJsonPathMissing
     * @dataProvider goodAssertJsonPathMissingData
     */
    public function assertJsonPathMissingIsSuccessfulWhenItemIsNotInResponse(string $key, mixed $item, array $actual): void
    {
        $response = new TestResponse(new Response($actual));

        $response->assertJsonPathMissing($key, $item);
    }

    public static function badAssertJsonPathMissingData()
    {
        return [
            [
                'key',
                1,
                ['key' => [1, 2, 3, 4]],
            ],
            [
                'key',
                ['foo' => 'bar'],
                ['key' => [['foo' => 'toto'], ['foo' => 'bar']]],
            ],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\JsonAssertions::assertJsonPathMissing
     * @dataProvider badAssertJsonPathMissingData
     */
    public function assertJsonPathMissingIsNotSuccessfulWhenDataContainsTheItem(string $key, mixed $item, array $actual): void
    {
        $response = new TestResponse(new Response($actual));

        $this->expectException(ExpectationFailedException::class);
        $response->assertJsonPathMissing($key, $item);
    }

    /**
     * @test
     * @covers \Soyhuce\Testing\TestResponse\JsonAssertions::assertJsonMessage
     */
    public function assertJsonMessageIsSuccessfulWhenMessageMatches(): void
    {
        $response = new TestResponse(new Response(['message' => 'Hello world']));

        $response->assertJsonMessage('Hello world');
    }

    public static function badAssertJsonMessageData()
    {
        return [
            [['message' => 'Hello foo']],
            [['foo' => 'Hello world']],
        ];
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\JsonAssertions::assertJsonMessage
     * @dataProvider badAssertJsonMessageData
     */
    public function assertJsonMessageFailsWhenMessageDoesNotMatch(array $content): void
    {
        $response = new TestResponse(new Response($content));

        $this->expectException(ExpectationFailedException::class);
        $response->assertJsonMessage('Hello world');
    }

    /**
     * @test
     * @covers \Soyhuce\Testing\TestResponse\JsonAssertions::assertSimplePaginated
     */
    public function assertSimplePaginatedIsSuccessfulWhenSimplePaginated(): void
    {
        $response = new TestResponse(new Response([
            'data' => [],
            'links' => [
                'first' => 'http://some.url/?page=1',
                'last' => null,
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 10,
                'path' => 'http://some.url/?page=1',
            ],
        ]));

        $response->assertSimplePaginated();
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\JsonAssertions::assertSimplePaginated
     */
    public function assertSimplePaginatedFailsWhenNotSimplePaginated(): void
    {
        $response = new TestResponse(new Response([
            'data' => [],
        ]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertSimplePaginated();
    }

    /**
     * @test
     * @covers \Soyhuce\Testing\TestResponse\JsonAssertions::assertPaginated
     */
    public function assertPaginatedIsSuccessfulWhenPaginated(): void
    {
        $response = new TestResponse(new Response([
            'data' => [],
            'links' => [
                'first' => 'http://some.url/?page=1',
                'last' => null,
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 10,
                'total' => 10,
                'path' => 'http://some.url/?page=1',
            ],
        ]));

        $response->assertPaginated();
    }

    /**
     * @test
     * @covers       \Soyhuce\Testing\TestResponse\JsonAssertions::assertPaginated
     */
    public function assertPaginatedFailsWhenNotPaginated(): void
    {
        $response = new TestResponse(new Response([
            'data' => [],
        ]));

        $this->expectException(ExpectationFailedException::class);
        $response->assertPaginated();
    }
}
