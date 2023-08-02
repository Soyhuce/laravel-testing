<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Fixtures\FormRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FileRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'image'],
        ];
    }
}
