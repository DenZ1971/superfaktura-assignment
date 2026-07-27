<?php

declare(strict_types=1);

namespace Den\AresCompanyRegistry;

use Den\AresCompanyRegistry\Dto\Company;
use Den\AresCompanyRegistry\Exception\CompanyNotFoundException;
use Den\AresCompanyRegistry\Exception\InvalidResponseException;
use Den\AresCompanyRegistry\Exception\RegistryUnavailableException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AresCompanyRegistry
{
    private const string API_URL = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly IcoValidator $validator,
    ) {
    }

    public function findByIco(string $ico): Company
    {
        $this->validator->validate($ico);

        try {
            $response = $this->httpClient->request(
                'GET',
                self::API_URL . $ico
            );

            $statusCode = $response->getStatusCode();

            if ($statusCode === 404) {
                throw CompanyNotFoundException::forIco($ico);
            }

            if ($statusCode < 200 || $statusCode >= 300) {
                throw new RegistryUnavailableException(
                    sprintf(
                        'ARES returned unexpected HTTP status %d.',
                        $statusCode
                    )
                );
            }

            $data = $response->toArray(false);

        } catch (TransportExceptionInterface $exception) {
            throw new RegistryUnavailableException(
                'Unable to communicate with the ARES registry.',
                previous: $exception
            );
        } catch (DecodingExceptionInterface $exception) {
            throw new InvalidResponseException(
                'ARES returned invalid JSON.',
                previous: $exception
            );
        }

        if (
            !isset($data['ico'], $data['obchodniJmeno'])
            || !is_string($data['ico'])
            || !is_string($data['obchodniJmeno'])
        ) {
            throw new InvalidResponseException(
                'ARES response does not contain the expected company data.'
            );
        }

        return new Company(
            ico: $data['ico'],
            name: $data['obchodniJmeno'],
        );
    }
}