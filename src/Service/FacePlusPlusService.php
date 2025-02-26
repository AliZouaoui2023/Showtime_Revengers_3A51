<?php

// src/Service/FacePlusPlusService.php
namespace App\Service;

use GuzzleHttp\Client;

class FacePlusPlusService
{
    private $client;
    private $apiKey;
    private $apiSecret;

    public function __construct($apiKey, $apiSecret)
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function detectFace($photoPath)
    {
        $response = $this->client->post('https://api-us.faceplusplus.com/facepp/v3/detect', [
            'multipart' => [
                [
                    'name' => 'api_key',
                    'contents' => $this->apiKey
                ],
                [
                    'name' => 'api_secret',
                    'contents' => $this->apiSecret
                ],
                [
                    'name' => 'image_file',
                    'contents' => fopen($photoPath, 'r')
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}