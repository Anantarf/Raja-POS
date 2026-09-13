<?php

namespace App\Support;

use InvalidArgumentException;

final class Rupiah
{
    public static function value(mixed $amount, string $field = 'Nominal'): int
    {
        if (is_int($amount)) {
            return $amount;
        }

        if (is_float($amount)) {
            if (! is_finite($amount) || floor($amount) !== $amount) {
                throw new InvalidArgumentException("{$field} harus berupa Rupiah utuh tanpa pecahan.");
            }

            return (int) $amount;
        }

        $normalized = trim((string) $amount);
        if (! preg_match('/^-?\d+(?:\.0{1,2})?$/', $normalized)) {
            throw new InvalidArgumentException("{$field} harus berupa Rupiah utuh tanpa pecahan.");
        }

        return (int) explode('.', $normalized, 2)[0];
    }
}
