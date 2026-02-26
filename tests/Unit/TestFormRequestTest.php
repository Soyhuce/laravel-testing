<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\TestsFormRequests;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\CreateUserRequest;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\FileRequest;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\UpdatePasswordRequest;
use Soyhuce\Testing\Tests\Fixtures\FormRequests\WithPrepareValidationRequest;
use Soyhuce\Testing\Tests\TestCase;

#[CoversClass(\Soyhuce\Testing\FormRequest\TestFormRequest::class)]
class TestFormRequestTest extends TestCase
{
    use TestsFormRequests;

    #[Test]
    public function theFormRequestIsValid(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->validate([
                'name' => 'John Doe',
                'email' => 'john.doe@email.com',
            ])
            ->assertPasses();
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function theFormRequestPassesValidationWithFile(): void
    {
        $this->createRequest(FileRequest::class)
            ->withFiles(['file' => UploadedFile::fake()->image('foo.jpg')])
            ->validate()
            ->assertPasses();
    }

    #[Test]
    public function theFormRequestFailsValidationWithFile(): void
    {
        $this->createRequest(FileRequest::class)
            ->validate()
            ->assertFails([
                'file' => 'required',
            ]);
    }

    #[Test]
    public function theFormRequestPassesAuthorization(): void
    {
        $this->createRequest(CreateUserRequest::class)
            ->assertAuthorized();
    }

    #[Test]
    public function theFormRequestFailsAuthorization(): void
    {
        Model::unguard();

        $this->createRequest(CreateUserRequest::class)
            ->by(new User(['id' => 1]))
            ->assertUnauthorized();
    }

    #[Test]
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

    #[Test]
    public function prepareForValidationIsCalled(): void
    {
        Model::unguard();

        $this->createRequest(WithPrepareValidationRequest::class)
            ->withParam('user', new User(['email' => 'peter.jackson']))
            ->validate()
            ->assertFails([
                'user_email' => 'The user email field must be a valid email address.',
            ]);

        $this->createRequest(WithPrepareValidationRequest::class)
            ->withParam('user', new User(['email' => 'peter.jackson@email.com']))
            ->validate()
            ->assertPasses()
            ->assertValidated([
                'user_email' => 'peter.jackson@email.com',
            ]);
    }
}
