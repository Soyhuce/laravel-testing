<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures;

use Soyhuce\Testing\Mock\SimpleMock;

class BaseClassMock extends BaseClass
{
    use SimpleMock;

    protected static function abstract(): string
    {
        return BaseClass::class;
    }

    public function execute(int $param): void
    {
        $this->verify($param);
    }

    public function executeAndReturns(string $param): string
    {
        return $this->verifyAndReturns($param);
    }
}
