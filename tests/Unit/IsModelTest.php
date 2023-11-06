<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\LaravelAssertions;
use Soyhuce\Testing\Tests\TestCase;

/**
 * @covers \Soyhuce\Testing\Constraints\IsModel
 */
class IsModelTest extends TestCase
{
    use LaravelAssertions;

    public static function newModel(array $attributes = []): Model
    {
        return new class($attributes) extends Model {
        };
    }

    public static function sameModel(): array
    {
        Model::unguard();

        return [
            [self::newModel(['id' => 1]), self::newModel(['id' => 1])],
            [new User(['id' => 1]), new User(['id' => 1])],
            [new User(['id' => 1, 'name' => 'John']), new User(['id' => 1, 'name' => 'Peter'])],
        ];
    }

    /**
     * @test
     * @dataProvider sameModel
     */
    public function modelsAreEqual(Model $first, Model $second): void
    {
        $this->assertIsModel($first, $second);
    }

    public static function differentModels(): array
    {
        Model::unguard();

        return [
            [self::newModel(['id' => 1]), self::newModel(['id' => 2])],
            [self::newModel(['id' => 1]), new User(['id' => 1])],
            [new User(['id' => 2]), new User(['id' => 1])],
        ];
    }

    /**
     * @test
     * @dataProvider differentModels
     */
    public function modelsAreDifferent(Model $first, Model $second): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->assertIsModel($first, $second);
    }
}
