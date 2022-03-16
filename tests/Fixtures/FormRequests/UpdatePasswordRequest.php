<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures\FormRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
