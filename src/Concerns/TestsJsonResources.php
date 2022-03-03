<?php declare(strict_types=1);

namespace Soyhuce\Testing\Concerns;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\TestResponse;

trait TestsJsonResources
{
    protected function createResponse(JsonResource $resource, ?Request $request = null): TestResponse
    {
        return new TestResponse($resource->response($request));
    }
}
