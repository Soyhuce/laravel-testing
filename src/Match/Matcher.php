<?php declare(strict_types=1);

namespace Soyhuce\Testing\Match;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Soyhuce\Testing\Assertions\LaravelAssert;
use function is_array;
use function is_callable;
use function is_object;

class Matcher
{
    public static function make(mixed ...$expected): Closure
    {
        return function (...$args) use ($expected) {
            foreach ($expected as $key => $value) {
                if ($value instanceof Model) {
                    $value = self::isModel($value);
                } elseif ($value instanceof Collection) {
                    $value = self::collectionEquals($value);
                } elseif (class_exists(\Spatie\LaravelData\Data::class) && $value instanceof \Spatie\LaravelData\Data) {
                    $value = self::dataEquals($value);
                }

                if (is_callable($value)) {
                    $result = $value($args[$key]);

                    if ($result === false) {
                        return false;
                    }
                } elseif (is_object($value) || is_array($value)) {
                    Assert::assertEquals($value, $args[$key]);
                } else {
                    Assert::assertSame($value, $args[$key]);
                }
            }

            return true;
        };
    }

    public static function isModel(Model $expected): Closure
    {
        return function (mixed $actual) use ($expected) {
            LaravelAssert::assertIsModel($expected, $actual);

            return true;
        };
    }

    /**
     * @param array<array-key, mixed>|\Illuminate\Support\Collection<array-key, mixed> $expected
     */
    public static function collectionEquals(Collection|array $expected): Closure
    {
        return function (mixed $actual) use ($expected) {
            LaravelAssert::assertCollectionEquals($expected, $actual);

            return true;
        };
    }

    public static function dataEquals(\Spatie\LaravelData\Data $expected): Closure
    {
        return function (mixed $actual) use ($expected) {
            LaravelAssert::assertDataEquals($expected, $actual);

            return true;
        };
    }

    /**
     * @param class-string $class
     */
    public static function of(string $class): ValueMatcher
    {
        return self::match($class, fn ($item) => $item::class);
    }

    /**
     * @param array<string, mixed>|array{mixed, callable} $args
     */
    public static function match(...$args): ValueMatcher
    {
        $matcher = new ValueMatcher();
        if (Arr::isAssoc($args)) {
            $matcher->properties(...$args);
        } else {
            [$expected, $transformation] = $args;
            $matcher->match($expected, $transformation);
        }

        return $matcher;
    }
}
