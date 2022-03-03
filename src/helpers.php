<?php declare(strict_types=1);

namespace Soyhuce\Testing;

use function func_get_args;
use function function_exists;

if (!function_exists('capture')) {
    /**
     * @template TReturn
     * @static
     * @param TReturn $result
     * @param callable(): TReturn $closure
     * @return callable(): TReturn
     */
    function capture(mixed &$result, callable $closure): callable
    {
        return function () use (&$result, $closure) {
            return $result = $closure(...func_get_args());
        };
    }
}
