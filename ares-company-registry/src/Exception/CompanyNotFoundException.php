<?php

declare(strict_types=1);

namespace Den\AresCompanyRegistry\Exception;

final class CompanyNotFoundException extends AresException
{
    public static function forIco(string $ico): self
    {
        return new self(
            sprintf('Company with IČO "%s" was not found.', $ico)
        );
    }
}