<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\TestsFormRequests;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\CreateUserRequest;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\UpdatePasswordRequest;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\Testing\FormRequest\TestFormRequest
 */
class TestFormRequestTest extends TestCase
{
    use TestsFormRequests;

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestIsValid(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
            ])
            ->assertPasses();
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestIsValidatesValidatedDaa(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
                'foo' => 'bar',
            ])
            ->assertPasses()
            ->assertValidated([
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
            ]);
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestFailsToBeInvalid(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
            ])
            ->assertFails();
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestIsInvalid(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => null,
                'email' => 'john doe',
            ])
            ->assertFails()
            ->assertFails([
                'name' => 'required',
                'email' => 'valid email address',
            ])
            ->assertFails([
                'name' => 'The name field is required.',
                'email' => 'must be a valid email address.',
            ]);
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestIsInvalidWithArrayOfMessages(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => 'John Doe',
                'email' => 12,
            ])
            ->assertFails([
                'email' => [
                    'must be a string.',
                    'must be a valid email address.',
                ],
            ]);
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestVerifiesTheMessage(): void
    {
        $this->expectException(AssertionFailedError::class);

        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => null,
                'email' => 'john doe',
            ])
            ->assertFails([
                'name' => 'foo',
            ]);
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestVerifiesAnArrayOfMessages(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches(
            '/Failed to find a validation error in the response for key and message: \'email\' => \'The email must be a string.\'/'
        );

        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => 'John Doe',
                'email' => 'john doe',
            ])
            ->assertFails([
                'email' => [
                    'The email must be a string.',
                ],
            ]);
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestFailsToBeValid(): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => null,
                'email' => 'john doe',
            ])
            ->assertPasses();
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestPassesAuthorization(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->assertAuthorized();
    }

    /**
     * @test
     * @covers ::validate
     */
    public function theFormRequestFailsAuthorization(): void
    {
        Model::unguard();

        $this->createRequest(CreateUserRequest::class)
            ->by(new User(['id' => 1]))
            ->assertUnauthorized();
    }

    /**
     * @test
     * @covers ::by
     */
    public function theUserIsInjectedInAuthGuard(): void
    {
        Model::unguard();

        $this->createRequest(UpdatePasswordRequest::class)
            ->by(new User(['id' => 1, 'password' => Hash::make('password')]))
            ->validate([
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertPasses();
    }
}
