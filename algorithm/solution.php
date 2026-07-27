<?php

declare(strict_types=1);

function formatNumber(int $number): string
{
    if ($number % 15 === 0){
        return 'SuperFaktura';
    }

    if ($number % 3 === 0){
        return 'Super';
    }

    if ($number % 5 === 0){
        return 'Faktura';
    }

    return (string) $number;
}

function printSequence(): void
{
    for ($number = 1; $number <=100; $number++) {
        echo formatNumber($number) . PHP_EOL;
    }
}

printSequence();