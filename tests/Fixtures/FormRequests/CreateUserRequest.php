<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures\FormRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null) {
            return true;
        }

        return $user->id !== 1;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
        ];
    }
}
