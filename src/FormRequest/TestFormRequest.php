<?php declare(strict_types=1);

namespace Soyhuce\Testing\FormRequest;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * @see https://gist.github.com/colindecarlo/9ba9bd6524127fee7580ae66c6d4709d
 *
 * @template TRequest of \Illuminate\Foundation\Http\FormRequest
 */
class TestFormRequest
{
    use Macroable;

    /**
     * @param TRequest $request
     */
    public function __construct(
        private FormRequest $request,
    ) {
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public function validate(array $data): TestValidationResult
    {
        $this->request->request = new InputBag($data);

        /** @var \Illuminate\Contracts\Validation\Validator $validator */
        $validator = invade($this->request)->getValidatorInstance();

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            return new TestValidationResult($validator, $e);
        }

        return new TestValidationResult($validator);
    }

    public function by(Authenticatable $user, ?string $guard = null): static
    {
        $this->request->setUserResolver(fn () => $user);
        Auth::guard($guard)->setUser($user);

        return $this;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function withParams(array $params): static
    {
        foreach ($params as $param => $value) {
            $this->withParam($param, $value);
        }

        return $this;
    }

    public function withParam(string $param, mixed $value): static
    {
        $this->request->route()->setParameter($param, $value);

        return $this;
    }

    public function assertAuthorized(): void
    {
        Assert::assertTrue(
            invade($this->request)->passesAuthorization(),
            'The provided user is not authorized by this request'
        );
    }

    public function assertUnauthorized(): void
    {
        Assert::assertFalse(
            invade($this->request)->passesAuthorization(),
            'The provided user is authorized by this request'
        );
    }

    /**
     * @return TRequest
     */
    public function request(): FormRequest
    {
        return $this->request;
    }
}
