<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\Testing\Concerns\TestsFormRequests;
use Soyhuce\Testing\FormRequest\TestFormRequest;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\CreateUserRequest;
use Soyhuce\Testing\Tests\TestCase;

#[CoversClass(TestsFormRequests::class)]
class TestsFormRequestsTest extends TestCase
{
    use TestsFormRequests;

    #[Test]
    public function formRequestIsCreated(): void
    {
        $this->assertInstanceOf(TestFormRequest::class, $this->createRequest(CreateUserRequest::class));
    }
}
