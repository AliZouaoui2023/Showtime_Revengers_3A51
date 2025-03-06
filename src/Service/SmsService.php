<?php
namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private Client $twilioClient;
    private string $twilioFrom;

    public function __construct(Client $twilioClient, string $twilioFrom)
    {
        $this->twilioClient = $twilioClient;
        $this->twilioFrom = $twilioFrom;
    }

    public function sendSms(string $to, string $message): void
    {
        try {
            $this->twilioClient->messages->create(
                $to,
                [
                    'from' => $this->twilioFrom,
                    'body' => $message,
                ]
            );
        } catch (\Exception $e) {
            // Capture les erreurs
            echo 'Erreur d\'envoi de SMS : ', $e->getMessage();
        }
    }
}