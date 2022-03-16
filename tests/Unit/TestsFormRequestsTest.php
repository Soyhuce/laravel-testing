<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Soyhuce\Testing\Concerns\TestsFormRequests;
use Soyhuce\Testing\FormRequest\TestFormRequest;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\CreateUserRequest;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @covers \Soyhuce\Testing\Concerns\TestsFormRequests
 */
class TestsFormRequestsTest extends TestCase
{
    use TestsFormRequests;

    /**
     * @test
     * @covers ::createRequest
     */
    public function formRequestIsCreated(): void
    {
        $this->assertInstanceOf(TestFormRequest::class, $this->createRequest(CreateUserRequest::class));
    }
}
