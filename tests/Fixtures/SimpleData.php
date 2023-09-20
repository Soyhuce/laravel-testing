<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures;

use Spatie\LaravelData\Data;

class SimpleData extends Data
{
    public function __construct(
        public string $name,
        public int $age,
    ) {
    }
}
