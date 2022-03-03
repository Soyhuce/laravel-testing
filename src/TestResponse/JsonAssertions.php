<?php declare(strict_types=1);

namespace Soyhuce\Testing\TestResponse;

use Closure;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * @mixin \Illuminate\Testing\TestResponse
 */
class JsonAssertions
{
    public function assertJsonPathMissing(): Closure
    {
        return function (string $path, $item): self {
            PHPUnit::assertNotContains($item, $this->json($path));

            return $this;
        };
    }

    public function assertJsonMessage(): Closure
    {
        return function (string $message): self {
            $this->assertJsonPath('message', $message);

            return $this;
        };
    }

    public function assertSimplePaginated(): Closure
    {
        return function (): self {
            $this->assertJsonStructure([
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'per_page', 'from', 'to', 'path'],
            ]);

            return $this;
        };
    }

    public function assertPaginated(): Closure
    {
        return function (): self {
            $this->assertJsonStructure([
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'from', 'to', 'total', 'path'],
            ]);

            return $this;
        };
    }
}
