<?php

/**
 * Format bytes to human-readable units (KB, MB, GB, TB).
 *
 * @param  int  $size      The size in bytes to be formatted.
 * @param  int  $precision The number of decimal places to display (default: 2).
 * @return string The formatted size with the appropriate unit.
 */
function formatBytes(int $size, int $precision = 2): string
{
    if ($size > 0) {
        $base = log($size, 1024);
        $suffixes = ['bytes', 'KB', 'MB', 'GB', 'TB'];

        return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
    } else {
        return '0 bytes';
    }
}

/**
 * Calculate the greatest common divisor (GCD) of two numbers.
 *
 * This function uses the Euclidean algorithm to find the largest number
 * that divides both of the given numbers without leaving a remainder.
 *
 * @param int $a The first number.
 * @param int $b The second number.
 * @return int The greatest common divisor of $a and $b.
 */
function gcd($a, $b) {
    return ($a % $b) ? gcd($b, $a % $b) : $b;
}
