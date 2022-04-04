<?php

namespace App\MarsRover;

use App\NasaApi\NasaApi;
use DateTime;
use GuzzleHttp\Client;

class MarsRoverImagesService
{

    private const API_ENDPOINT = 'mars-photos/api/v1/rovers/curiosity/photos';

    private string $imagesDirPath;
    private NasaApi $nasaApiClient;
    private Client $httpClient;

    public function __construct(
        string $imagesDirPath,
        NasaApi $nasaApiClient,
        Client $httpClient
    ) {
        $this->imagesDirPath = $imagesDirPath;
        $this->nasaApiClient = $nasaApiClient;
        $this->httpClient = $httpClient;
    }

    public function fetchImagesForDay(DateTime $date): void
    {
        $earthDate = $date->format('Y-n-j');

        $data = $this->nasaApiClient->sendRequest(
            self::API_ENDPOINT,
            [
                'earth_date' => $earthDate
            ]
        );

        if (!file_exists($this->imagesDirPath)) {
            mkdir($this->imagesDirPath);
        }

        $photoDirPath = $this->imagesDirPath . '/' . $earthDate;

        if (!file_exists($photoDirPath)) {
            mkdir($photoDirPath);
        }

        foreach ($data['photos'] as $photoData) {
            $this->downloadImage($photoDirPath, $photoData['img_src']);
        }
    }

    private function downloadImage(string $dirPath, string $imageUrl): void
    {
        $photoUrlParts = explode('/', $imageUrl);
        $photoName = end($photoUrlParts);
        $photoPath = $dirPath . '/' . $photoName;

        if (file_exists($photoPath)) {
            return;
        }

        $this->httpClient->get($imageUrl, ['sink' => $photoPath]);
    }
}