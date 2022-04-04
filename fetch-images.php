<?php

use App\MarsRover\MarsRoverImagesService;
use App\NasaApi\NasaApi;
use GuzzleHttp\Client;

require_once __DIR__ . '/vendor/autoload.php';

$config = yaml_parse_file(__DIR__ . '/config.yaml');
$apiKey = $config['api_key'];
$imagesDirectoryPath = __DIR__ . '/images';

if (!file_exists($imagesDirectoryPath)) {
    mkdir($imagesDirectoryPath);
}

if ($argc < 2) {
    echo "Please provide a date to download photos for\n";
    exit(1);
}

$date = DateTime::createFromFormat('Y-n-j', $argv[1]);

if (!$date) {
    echo "Please provide date in the following format yyyy-m-d\n";
    exit(1);
}

$httpClient = new Client();
$nasaApi = new NasaApi($apiKey, $httpClient);
$marsRoverImagesService = new MarsRoverImagesService(
    $imagesDirectoryPath,
    $nasaApi,
    $httpClient
);
$marsRoverImagesService->fetchImagesForDay($date);