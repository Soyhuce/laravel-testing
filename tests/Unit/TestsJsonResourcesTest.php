<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Soyhuce\Testing\Concerns\TestsJsonResources;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\Testing\Concerns\TestsJsonResources
 */
class TestsJsonResourcesTest extends TestCase
{
    use TestsJsonResources;

    /**
     * @test
     * @covers ::fromResource
     */
    public function theTestResponseIsCreatedFromTheResource(): void
    {
        Model::unguard();
        $user = new User(['id' => 1, 'name' => 'John Doe']);
        $resource = new class($user) extends JsonResource {
            public function toArray($request)
            {
                return [
                    'id' => $this->id,
                    'name' => $this->name,
                ];
            }
        };

        $this->createResponse($resource)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'John Doe',
                ],
            ]);
    }
}
