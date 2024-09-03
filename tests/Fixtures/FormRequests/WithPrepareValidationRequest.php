<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures\FormRequests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read \Illuminate\Foundation\Auth\User $user
 */
class WithPrepareValidationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_email' => ['required', 'string', 'email'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_email' => $this->user->email,
        ]);
    }
}
