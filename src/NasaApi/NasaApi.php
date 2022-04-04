<?php

namespace App\NasaApi;

use Exception;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;

class NasaApi
{

    private const HOST = 'https://api.nasa.gov';

    private string $apiKey;
    private ClientInterface $httpClient;

    public function __construct(string $apiKey, ClientInterface $httpClient)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $apiEndpoint
     * @param array $queryParameters
     * @return array
     * @throws NasaApiException
     */
    public function sendRequest(
        string $apiEndpoint,
        array $queryParameters
    ): array {
        $queryParameters['api_key'] = $this->apiKey;
        $request = new Request(
            'get',
            self::HOST . '/' . $apiEndpoint . '?' . http_build_query($queryParameters)
        );

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (Exception $ex) {
            throw new NasaApiException('An error occurred while communicating with NASA API', 0, $ex);
        }

        try {
            return json_decode($response->getBody()->getContents(), true, JSON_THROW_ON_ERROR);
        } catch (Exception $ex) {
            throw new NasaApiException('Failed to parse response as JSON', 0, $ex);
        }
    }

}