<?php

namespace App\Services;


use VK\Client\VKApiClient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class AuthVK
{
    private $client;

    public function __construct(VKApiClient $client)
    {
        $this->client = $client;
    }

    public function check($token)
    {
        try {
            $response = $this->client->account()->setOnline($token, [
                "voip" => 0
            ]);
        } catch (\Exception $exception) {
            $response = $exception;
        }
        return $response;
    }
}


