<?php

namespace App\Service;

use GuzzleHttp\Client;

class FacePlusPlusService
{
    private $client;
    private $apiKey;
    private $apiSecret;

    public function __construct(string $apiKey, string $apiSecret)
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

    public function compareFaces($faceToken1, $faceToken2)
    {
        $response = $this->client->post('https://api-us.faceplusplus.com/facepp/v3/compare', [
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
                    'name' => 'face_token1',
                    'contents' => $faceToken1
                ],
                [
                    'name' => 'face_token2',
                    'contents' => $faceToken2
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function createFaceSet($outerId)
    {
        $maxRetries = 3;
        $retryCount = 0;
    
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->post('https://api-us.faceplusplus.com/facepp/v3/faceset/create', [
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
                            'name' => 'outer_id',
                            'contents' => $outerId
                        ]
                    ]
                ]);
    
                $result = json_decode($response->getBody(), true);
    
                // Si le faceset existe déjà, ignorer l'erreur
                if (isset($result['error_message']) && strpos($result['error_message'], 'FACESET_EXIST') === false) {
                    throw new \Exception($result['error_message']);
                }
    
                return $result;
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'CONCURRENCY_LIMIT_EXCEEDED') !== false) {
                    $retryCount++;
                    sleep(1); // Attendre 1 seconde avant de réessayer
                } else {
                    throw $e; // Relancer l'exception si l'erreur est autre que CONCURRENCY_LIMIT_EXCEEDED
                }
            }
        }
    
        throw new \Exception('Failed to create face set after ' . $maxRetries . ' attempts.');
    }

    public function addFaceToSet($outerId, $faceToken)
    {
        $maxRetries = 3;
        $retryCount = 0;
    
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->post('https://api-us.faceplusplus.com/facepp/v3/faceset/addface', [
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
                            'name' => 'outer_id',
                            'contents' => $outerId
                        ],
                        [
                            'name' => 'face_tokens',
                            'contents' => $faceToken
                        ]
                    ]
                ]);
    
                return json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'CONCURRENCY_LIMIT_EXCEEDED') !== false) {
                    $retryCount++;
                    sleep(1); // Attendre 1 seconde avant de réessayer
                } else {
                    throw $e; // Relancer l'exception si l'erreur est autre que CONCURRENCY_LIMIT_EXCEEDED
                }
            }
        }
    
        throw new \Exception('Failed to add face to set after ' . $maxRetries . ' attempts.');
    }

    public function searchFace($outerId, $faceToken)
    {
        $maxRetries = 3;
        $retryCount = 0;
    
        while ($retryCount < $maxRetries) {
            try {
                $response = $this->client->post('https://api-us.faceplusplus.com/facepp/v3/search', [
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
                            'name' => 'outer_id',
                            'contents' => $outerId
                        ],
                        [
                            'name' => 'face_token',
                            'contents' => $faceToken
                        ]
                    ]
                ]);
    
                return json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'CONCURRENCY_LIMIT_EXCEEDED') !== false) {
                    $retryCount++;
                    sleep(1); // Attendre 1 seconde avant de réessayer
                } else {
                    throw $e; // Relancer l'exception si l'erreur est autre que CONCURRENCY_LIMIT_EXCEEDED
                }
            }
        }
    
        throw new \Exception('Failed to search face after ' . $maxRetries . ' attempts.');
    }
}