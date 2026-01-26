<?php declare(strict_types=1);

namespace Soyhuce\Testing\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use Soyhuce\Testing\Concerns\LaravelAssertions;
use Soyhuce\Testing\Tests\TestCase;

#[CoversClass(\Soyhuce\Testing\Constraints\IsModel::class)]
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

    #[Test]
    #[DataProvider('sameModel')]
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

    #[Test]
    #[DataProvider('differentModels')]
    public function modelsAreDifferent(Model $first, Model $second): void
    {
        $this->expectException(ExpectationFailedException::class);

        $this->assertIsModel($first, $second);
    }
}
