<?php declare(strict_types=1);

namespace Soyhuce\Testing\TestResponse;

use Closure;
use PHPUnit\Framework\Assert as PHPUnit;
use stdClass;

/**
 * @mixin \Illuminate\Testing\TestResponse
 * @method self assertValidRequest()
 * @method self assertValidResponse(int $status)
 */
class ContractAssertions
{
    public function assertValidContract(): Closure
    {
        return function (int $status): self {
            if ($this->baseResponse->headers->get('content-type') !== 'application/json') {
                $this->assertValidRequest()
                    ->assertValidResponse($status);

                return $this;
            }

            $placeholder = new stdClass();
            $data = data_get($this->decodeResponseJson()->json(), 'data', $placeholder);

            if ($data !== $placeholder) {
                PHPUnit::assertNotEmpty(
                    $data,
                    'data is empty.' . PHP_EOL .
                    'Please fix the test setup to ensure the call will return some data.' . PHP_EOL .
                    'If the route is supposed to return empty data, do not use assertValidContract.'
                );
            }

            $this->assertValidRequest()
                ->assertValidResponse($status);

            return $this;
        };
    }
}
