<?php declare(strict_types=1);

namespace Soyhuce\Testing\Concerns;

use Soyhuce\Testing\Mock\PendingActionMock;

trait MocksActions
{
    /**
     * @param class-string $action
     */
    protected function mockAction(string $action): PendingActionMock
    {
        return new PendingActionMock($action);
    }
}
