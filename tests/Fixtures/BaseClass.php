<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures;

use Exception;

class BaseClass
{
    public function execute(int $param): void
    {
        throw new Exception('This should not be called');
    }

    public function executeAndReturns(string $value): string
    {
        throw new Exception('This should not be called');
    }
}
