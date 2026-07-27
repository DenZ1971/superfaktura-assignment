<?php

declare(strict_types=1);

namespace Den\AresCompanyRegistry\Dto;

final readonly class Company
{
    public function __construct(
        public string $ico,
        public string $name,
    ) {
    }
}