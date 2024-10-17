# Helpers for Laravel tests

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/laravel-testing.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-testing)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/soyhuce/laravel-testing/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/soyhuce/laravel-testing/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/soyhuce/laravel-testing/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/soyhuce/laravel-testing/actions/workflows/phpstan.yml)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/soyhuce/laravel-testing/php-cs-fixer.yml?branch=main&label=php-cs-fixer&style=flat-square)](https://github.com/soyhuce/laravel-testing/actions/workflows/php-cs-fixer.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/laravel-testing.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-testing)

Extra tools for your laravel tests

## Installation

You can install the package via composer:

```bash
composer require soyhuce/laravel-testing --dev
```

## Usage

### Laravel assertions

To use Laravel specific assertions, you will have to add `\Soyhuce\Testing\Assertions\LaravelAssertions::class` trait to your test class.

#### assertModelIs

Matches if the model is equal to the given model.

```php
/** @test */
public function myTest()
{
    $user1 = User::factory()->createOne();
    $user2 = User::find($user1->id);
    
    $this->assertIsModel($user1, $user2);
}
```

#### assertCollectionEquals

Matches if the collections are equal.

```php
$collection1 = new Collection(['1', '2', '3']);
$collection2 = new Collection(['1', '2', '3']);
$this->assertCollectionEquals($collection1, $collection2);
```

2 Collections are considered equal if they contain the same elements, indexed by the same keys and in the same order.

```php
$this->assertCollectionEquals(new Collection([1, 2]), new Collection([1, 2, 3])); // fail
$this->assertCollectionEquals(new Collection([1, 2, 3]), new Collection([1, 2])); // fail
$this->assertCollectionEquals(new Collection([1, 2, 3]), new Collection([3, 1, 2])); // fail
$this->assertCollectionEquals(new Collection([1, 2, 3]), new Collection([3, 1, 2])); // fail
$this->assertCollectionEquals(new Collection([1, 2, 3]), new Collection([1, 2, "3"])); // fail
$this->assertCollectionEquals(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2])); // fail
$this->assertCollectionEquals(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'c' => 4])); // fail
$this->assertCollectionEquals(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'd' => 3])); // fail
$this->assertCollectionEquals(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'c' => 3, 'b' => 2])); // fail
$this->assertCollectionEquals(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 3])); // fail
```

If the Collections contain Models, `assertCollectionEquals` will use Model comparison of `assertIsModel`.

```php
$user1 = User::factory()->createOne();
$user2 = User::find($user1->id);
$this->assertCollectionEquals(collect([$user1]), collect([$user2])); // Success
```

You can give an array in the `$expected` parameter of `assertCollectionEquals` :

```php
/** @test */
public function theUsersAreOrdered(): void
{
    $user1 = User::factory()->createOne();
    $user2 = User::factory()->createOne();
    
    $this->assertCollectionEquals(
        [$user1, $user2],
        User::query()->orderByDesc('id')->get()
    );
} 
```

#### assertCollectionEqualsCanonicalizing

Matches if the collections are canonically equals.

```php
$collection1 = new Collection(['1', '2', '3']);
$collection2 = new Collection(['3', '2', '1']);
$this->assertCollectionEqualsCanonicalizing($collection1, $collection2);
```

2 Collections are considered equal if they contain the same elements, indexed by the same keys.

```php
$this->assertCollectionEqualsCanonicalizing(new Collection([1, 2]), new Collection([1, 2, 3])); // fail
$this->assertCollectionEqualsCanonicalizing(new Collection([1, 2, 3]), new Collection([1, 2])); // fail
$this->assertCollectionEqualsCanonicalizing(new Collection([1, 2, 3]), new Collection([1, 2, "3"])); // fail
$this->assertCollectionEqualsCanonicalizing(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2])); // fail
$this->assertCollectionEqualsCanonicalizing(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 'c' => 4])); // fail
$this->assertCollectionEqualsCanonicalizing(new Collection(['a' => 1, 'b' => 2, 'c' => 3]), new Collection(['a' => 1, 'b' => 2, 3])); // fail
```

If the Collections contain Models, `assertCollectionEqualsCanonicalizing` will use Model comparison of `assertIsModel`.

```php
$user1 = User::factory()->createOne();
$user2 = User::find($user1->id);
$this->assertCollectionEqualsCanonicalizing(collect([$user1]), collect([$user2])); // Success
```

You can give an array in the `$expected` parameter of `assertCollectionEqualsCanonicalizing` :

```php
/** @test */
public function theUsersAreOrdered(): void
{
    $user1 = User::factory()->createOne();
    $user2 = User::factory()->createOne();
    
    $this->assertCollectionEqualsCanonicalizing(
        [$user1, $user2],
        User::query()->get()
    );
} 
```

### TestResponse assertions

All these methods are available in `Illuminate\Testing\TestResponse`:

#### Contract Testing

Requires [hotmeteor/spectator](https://github.com/hotmeteor/spectator/) package

- `TestResponse::assertValidContract(int $status)` : Verifies that the request and the response are valid according to the contract.

#### Data

- `TestResponse::assertData($expect)` : Alias for `assertJsonPath('data', $expect)`
- `TestResponse::assertDataPath(string $path, $expect)` : Alias for `assertJsonPath('data.'.$path, $expect)`
- `TestResponse::assertDataPaths(array $expectations)` : Runs `assertDataPath` for each `$path` => `$expect` pair in the array.
- `TestResponse::assertDataMissing($item)` : Alias for `assertJsonMissingPath('data', $item)`
- `TestResponse::assertDataPathMissing(string $path, $item)` : Alias for `assertJsonMissingPath('data.'.$path, $item)`

#### Json

- `TestResponse::assertJsonPathMissing(string $path, $item)` : Verifies that the Json path does not contain `$item`
- `TestResponse::assertJsonMessage(string $message)` : Alias for `assertJsonPath('message', $message)`
- `TestResponse::assertSimplePaginated()` : Verifies that the response is a simple paginated response.
- `TestResponse::assertPaginated()` : Verifies that the response is a paginated response.

#### View

- `TestResponse::assertViewHasNull(string $key)` : Verifies that the key is present in the view but is null.

### FormRequest test in isolation

It's possible to test FormRequests in isolation thanks to the `TestsFormRequests` trait.

```php
$testFormRequest = $this->createRequest(CreateUserRequest::class);
```

`$testFormRequest` have some methods to check authorization and validation of the request.

- `TestFormRequest::by(Authenticable $user, ?string $guard = null)` : set authenticated user in the request
- `TestFormRequest::withParams(array $params)` : set route parameters
- `TestFormRequest::withParam(string $param, mixed $value)` : set a route parameter
- `TestFormRequest::validate(array $data): TestValidationResult` : get Validation result
- `TestFormRequest::assertAuthorized()` : assert that the request is authorized
- `TestFormRequest::assertUnauthorized()` : assert that the request is unauthorized
- `TestValidationResult::assertPasses()` : assert that the validation passes
- `TestValidationResult::assertFails(array $errors = [])` : assert that the validation fails
- `TestValidationResult::assertValidated(array $expected)` : assert that the attributes and values that passed validation are the expected ones

For exemple :

```php
$this->createRequest(CreateUserRequest::class)
    ->validate([
        'name' => 'John Doe',
        'email' => 'john.doe@email.com',
    ])
    ->assertPasses();

$this->createRequest(CreateUserRequest::class)
    ->validate([
        'name' => null,
        'email' => 12,
    ])
    //->assertFails() We can check that the validation fails without defining the fields nor error messages
    ->assertFails([
        'name' => 'The name field is required.',
        'email' => [
            'The email must be a string.',
            'The email must be a valid email address.',
        ]
    ]);

$this->createRequest(CreateUserRequest::class)
    ->by($admin)
    ->assertAuthorized();

$this->createRequest(CreateUserRequest::class)
    ->by($user)
    ->assertUnauthorized();

$this->createRequest(UpdateUserRequest::class)
    ->withArg('user', $user)
    ->validate([
        'email' => 'foo@email.com'
    ])
    ->assertPasses();
```

### JsonResource test in isolation

It's possible to test the `JsonResources` in isolation thanks to the `TestsJsonResources` trait.

`TestsJsonResources::createResponse(JsonResource $resource, ?Request $request = null)` returns a `Illuminate\Testing\TestResponse`.

```php
$this->createResponse(UserResource::make($user))
    ->assertData([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);
```

### Matcher

Let's take this test

```php
$user = User::factory()->createOne();

$this->mock(DeleteUser::class)
    ->shouldReceive('execute')
    ->withArgs(function(User $executed) use ($user) {
        $this->assertIsModel($user, $executed);
        
        return true;
    })
    ->once();

// run some code wich will execute the mock
```

We can simplify this test by using a `Matcher`.

```php
$this->mock(DeleteUser::class)
    ->shouldReceive('execute')
    ->withArgs(Matcher::isModel($user))
    ->once();
```

For Collections, we can use `Matcher::collectionEquals()`.

For more complex cases, we can use `Matcher::make`.

```php
$user = User::factory()->createOne();
$roles = Role::factory(2)->create();

$this->mock(UpdateUser::class)
    ->shouldReceive('execute')
    ->withArgs(function(User $executed, string $email, Collection $executedRoles) use ($user, $roles) {
        $this->assertIsModel($user, $executed);
        $this->assertSame('foo@email.com', $email);
        $this->assertCollectionEquals($roles, $executedRoles);
        return true;
    })
    ->once();

// Refactored to
$this->mock(UpdateUser::class)
    ->shouldReceive('execute')
    ->withArgs(Matcher::make(
        $user,
        'foo@email.com',
        $roles
    ))
    ->once();
```

#### Partial match

In some cases, we wish to check only a few arguments or call argument methods: 

```php
$this->mock(CreateUser::class)
    ->shouldReceive('execute')
    ->withArgs(function(UserDTO $data, Collection $executedRoles) use ($team, $roles) {
        $this->assertSame('foo@email.com', $data->email);
        $this->assertSame('password', $data->password);
        $this->assertIsModel($team, $data->team())
        $this->assertCollectionEquals($roles, $executedRoles);
        return true;
    })
    ->once();
```

We can use `Matcher::match` to define our assertions on `$data`:

```php
$this->mock(CreateUser::class)
    ->shouldReceive('execute')
    ->withArgs(Matcher::make(
        Matcher::match('foo@email.com', fn(UserDTO $data) => $data->email)
            ->match('password', fn(UserDTO $data) => $data->password)
            ->match($team, fn(UserDTO $data) => $data->team()),
        $roles
    ))
    ->once();
```

In specific cases of object properties, we can use named parameters:

```php
$this->mock(CreateUser::class)
    ->shouldReceive('execute')
    ->withArgs(Matcher::make(
        Matcher::match(email: 'foo@email.com', password: 'password')->match($team, fn(UserDTO $data) => $data->team()),
        $roles
    ))
    ->once();
```

We can also check object type:

```php
$this->mock(CreateUser::class)
    ->shouldReceive('execute')
    ->withArgs(Matcher::make(
        Matcher::of(UserDTO::class)->properties(email: 'foo@email.com', password: 'password'),
        $roles
    ))
    ->once();
```

### ActionMock

The trait `MocksActions` provides a `mockAction` method to simply mock an action. By convention, an action is a class with a `execute` method.

Under the hood, it uses `Mockery::mock`.

It allows to easily define your action's expectations. Instead of  
```php
$user = User::factory()->createOne();

$this->mock(DeleteUser::class)
    ->shouldReceive('execute')
    ->withArgs(function(User $executed) use ($user) {
        $this->assertIsModel($user, $executed);
        
        return true;
    })
    ->once();
```
you can write
```php
$user = User::factory()->createOne();

$this->mockAction(DeleteUser::class)
   ->with($user);
```

You can also define the return value and capture it to use it in your test.

```php
$this->mockAction(CreateUser::class)
    ->with(new UserData(email: 'john.doe@email.com', password: 'password'))
    ->returns(fn() => User::factory()->createOne())
    ->in($user);

$this->postJson('register', ['email' => 'john.doe@email.com', 'password' => 'password'])
    ->assertCreated()
    ->assertJson([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);
```

### Helpers

It can be necessary to capture the return value of a callback, for exemple in `returnUsing` of mocks.

```php
$this->mock(CreateOrUpdateVersion::class)
    ->expects('execute')
    ->andReturnUsing(
        fn () => Version::factory()->for($package)->createOne()
    )
    ->once();

// I need created Version ! How do I do ?
```

In this case, we will use `capture` function:

```php
$this->mock(CreateOrUpdateVersion::class)
    ->expects('execute')
    ->andReturnUsing(capture(
        $version,
        fn () => Version::factory()->for($package)->createOne()
    ))    
    ->once();
```

Once the mock executed, `$version` is created and will contain the returned value of the callback.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Colin DeCarlo](https://github.com/colindecarlo) for the FormRequest isolation testing
- [Bastien Philippe](https://github.com/bastien-phi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
