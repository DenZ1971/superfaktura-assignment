<?php

declare(strict_types=1);

namespace Den\AresCompanyRegistry\Tests;

use Den\AresCompanyRegistry\IcoValidator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class IcoValidatorTest extends TestCase
{
    private IcoValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new IcoValidator();
    }

    public function testValidIco(): void
    {
        $this->validator->validate('01569651');

        $this->addToAssertionCount(1);
    }

    public function testThrowsExceptionForTooShortIco(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate('1234567');
    }

    public function testThrowsExceptionForTooLongIco(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate('123456789');
    }

    public function testThrowsExceptionForNonNumericIco(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate('12AB5678');
    }

    public function testThrowsExceptionForEmptyIco(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate('');
    }

    public function testThrowsExceptionForInvalidChecksum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'IČO has an invalid checksum.'
        );

        $this->validator->validate('01569652');
    }
}