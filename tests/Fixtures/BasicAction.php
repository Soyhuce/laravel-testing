<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures;

class BasicAction
{
    public function execute(int $value): int
    {
        return $value;
    }
}
