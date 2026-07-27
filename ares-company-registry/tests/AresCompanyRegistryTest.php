<?php

declare(strict_types=1);

namespace Den\AresCompanyRegistry\Tests;

use Den\AresCompanyRegistry\AresCompanyRegistry;
use Den\AresCompanyRegistry\Exception\CompanyNotFoundException;
use Den\AresCompanyRegistry\Exception\InvalidResponseException;
use Den\AresCompanyRegistry\Exception\RegistryUnavailableException;
use Den\AresCompanyRegistry\IcoValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class AresCompanyRegistryTest extends TestCase
{
    public function testReturnsCompanyForValidResponse(): void
    {
        $registry = $this->createRegistry(
            new MockResponse(
                json_encode([
                    'ico' => '01569651',
                    'obchodniJmeno' => 'Test Company s.r.o.',
                ], JSON_THROW_ON_ERROR),
                [
                    'http_code' => 200,
                ]
            )
        );

        $company = $registry->findByIco('01569651');

        self::assertSame('01569651', $company->ico);
        self::assertSame('Test Company s.r.o.', $company->name);
    }

    public function testThrowsExceptionWhenCompanyIsNotFound(): void
    {
        $registry = $this->createRegistry(
            new MockResponse('', [
                'http_code' => 404,
            ])
        );

        $this->expectException(CompanyNotFoundException::class);
        $this->expectExceptionMessage(
            'Company with IČO "01569651" was not found.'
        );

        $registry->findByIco('01569651');
    }

    public function testThrowsExceptionForServerError(): void
    {
        $registry = $this->createRegistry(
            new MockResponse('', [
                'http_code' => 500,
            ])
        );

        $this->expectException(RegistryUnavailableException::class);
        $this->expectExceptionMessage(
            'ARES returned unexpected HTTP status 500.'
        );

        $registry->findByIco('01569651');
    }

    public function testThrowsExceptionForInvalidJson(): void
    {
        $registry = $this->createRegistry(
            new MockResponse(
                'this is not valid JSON',
                [
                    'http_code' => 200,
                ]
            )
        );

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage(
            'ARES returned invalid JSON.'
        );

        $registry->findByIco('01569651');
    }

    public function testThrowsExceptionWhenRequiredFieldsAreMissing(): void
    {
        $registry = $this->createRegistry(
            new MockResponse(
                json_encode([
                    'ico' => '01569651',
                ], JSON_THROW_ON_ERROR),
                [
                    'http_code' => 200,
                ]
            )
        );

        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage(
            'ARES response does not contain the expected company data.'
        );

        $registry->findByIco('01569651');
    }

    public function testSendsCorrectRequest(): void
    {
        $httpClient = new MockHttpClient(
            function (
                string $method,
                string $url,
                array $options
            ): MockResponse {
                self::assertSame('GET', $method);

                self::assertSame(
                    'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/01569651',
                    $url
                );

                return new MockResponse(
                    json_encode([
                        'ico' => '01569651',
                        'obchodniJmeno' => 'Test Company s.r.o.',
                    ], JSON_THROW_ON_ERROR),
                    [
                        'http_code' => 200,
                    ]
                );
            }
        );

        $registry = new AresCompanyRegistry(
            $httpClient,
            new IcoValidator()
        );

        $registry->findByIco('01569651');
    }

    public function testPreservesPreviousTransportException(): void
    {
        $response = new MockResponse(
            '',
            [
                'error' => 'Connection timed out.',
            ]
        );

        $registry = new AresCompanyRegistry(
            new MockHttpClient($response),
            new IcoValidator()
        );

        try {
            $registry->findByIco('01569651');

            self::fail(
                'RegistryUnavailableException was not thrown.'
            );
        } catch (RegistryUnavailableException $exception) {
            self::assertSame(
                'Unable to communicate with the ARES registry.',
                $exception->getMessage()
            );

            self::assertNotNull($exception->getPrevious());

            self::assertInstanceOf(
                TransportExceptionInterface::class,
                $exception->getPrevious()
            );

            self::assertSame(
                'Connection timed out.',
                $exception->getPrevious()->getMessage()
            );
        }
    }

    private function createRegistry(
        MockResponse $response
    ): AresCompanyRegistry {
        return new AresCompanyRegistry(
            new MockHttpClient($response),
            new IcoValidator()
        );
    }
}