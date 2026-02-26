<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Tests\TestCase;

#[CoversNothing]
class ViewAssertionsTest extends TestCase
{
    #[Test]
    public function assertAssertViewHasNullIsSuccessfulWhenDataValueIsNull(): void
    {
        $response = new TestResponse(
            (new Response())->setContent(
                view('welcome', ['value' => null])
            )
        );

        $response->assertViewHasNull('value');
    }

    #[Test]
    public function assertAssertViewHasNullIsSuccessfulWhenDataValueIsNotNull(): void
    {
        $response = new TestResponse(
            (new Response())->setContent(
                view('welcome', ['value' => 'foo'])
            )
        );

        $this->expectException(ExpectationFailedException::class);
        $response->assertViewHasNull('value');
    }

    #[Test]
    public function assertAssertViewHasNullIsSuccessfulWhenDataValueIsNotGiven(): void
    {
        $response = new TestResponse(
            (new Response())->setContent(
                view('welcome')
            )
        );

        $this->expectException(ExpectationFailedException::class);
        $response->assertViewHasNull('value');
    }
}
