<?php

declare(strict_types=1);

namespace Den\AresCompanyRegistry;

use InvalidArgumentException;

final class IcoValidator
{
    private const array WEIGHTS = [8, 7, 6, 5, 4, 3, 2];

    public function validate(string $ico): void
    {
        if (!preg_match('/^\d{8}$/', $ico)) {
            throw new InvalidArgumentException(
                'IČO must contain exactly 8 digits.'
            );
        }

        if (!$this->hasValidChecksum($ico)) {
            throw new InvalidArgumentException(
                'IČO has an invalid checksum.'
            );
        }
    }

    private function hasValidChecksum(string $ico): bool
    {
        $sum = 0;

        foreach (self::WEIGHTS as $position => $weight) {
            $sum += (int) $ico[$position] * $weight;
        }

        $remainder = $sum % 11;

        $expectedCheckDigit = match ($remainder) {
            0 => 1,
            1 => 0,
            default => 11 - $remainder,
        };

        $actualCheckDigit = (int) $ico[7];

        return $actualCheckDigit === $expectedCheckDigit;
    }
}