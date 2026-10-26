# ARES Company Registry   

A PHP 8.3+ library for retrieving company information from the Czech ARES (Administrative Register of Economic Subjects).

This project was created as part of a PHP developer technical assignment and demonstrates:

- object-oriented design;
- dependency injection;
- HTTP client integration;
- exception handling;
- unit testing with PHPUnit;
- modern PHP 8.3 features.

## Requirements

- PHP 8.3+
- Composer

## Installation

```bash
cd ares-company-registry
composer install
```

## Usage

```php
use Den\AresCompanyRegistry\AresCompanyRegistry;
use Den\AresCompanyRegistry\IcoValidator;
use Symfony\Component\HttpClient\HttpClient;

$registry = new AresCompanyRegistry(
    HttpClient::create(),
    new IcoValidator()
);

$company = $registry->findByIco('01569651');

echo $company->ico . PHP_EOL;
echo $company->name . PHP_EOL;
```

## Validation

The library validates the supplied IČO before sending a request to ARES.

Validation includes:

- exactly 8 digits;
- valid checksum.

An `InvalidArgumentException` is thrown if the supplied IČO is invalid.

## Exceptions

The library may throw the following exceptions:

| Exception | Description |
|----------|-------------|
| `InvalidArgumentException` | Invalid IČO format or checksum |
| `CompanyNotFoundException` | Company was not found |
| `RegistryUnavailableException` | Network error or unexpected HTTP status |
| `InvalidResponseException` | Invalid or incomplete response from ARES |

## Running tests

```bash
vendor/bin/phpunit tests --testdox
```

Run static analysis:

```bash
composer analyse
```

## Project structure

```
src/
├── Dto/
├── Exception/
├── AresCompanyRegistry.php
└── IcoValidator.php

tests/
```

## Features

- Company lookup by IČO
- IČO format and checksum validation
- Symfony HttpClient
- Dependency Injection
- DTO-based API
- Custom exception hierarchy
- PHPUnit 11 test suite
- PHPStan 2 static analysis
- Mock HTTP testing


## License

This project was created as part of a technical assignment.
