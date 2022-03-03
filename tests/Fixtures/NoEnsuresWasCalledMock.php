<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures;

use Soyhuce\Testing\Mock\SimpleMock;

class NoEnsuresWasCalledMock extends BaseClass
{
    use SimpleMock;

    protected bool $ensuresWasCalled = false;

    protected static function abstract(): string
    {
        return BaseClass::class;
    }
}
