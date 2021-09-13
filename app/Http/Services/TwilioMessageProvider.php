<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class TwilioMessageProvider implements MessageProvider
{
    /** @var string */
    private $id;

    /** @var string */
    private $token;

    /** @var Client */
    private $client;

    public function __construct()
    {
        $this->id       = env('TWILIO_KEY');
        $this->token    = env('TWILIO_TOKEN');
        $this->client   = new Client($this->id, $this->token);
    }

    /**
     * @throws \Exception
     */
    public function validateNumber(Request $request)
    {
        if (!$request->has('phone')) {
            throw new \Exception('Missing required parameter: phone');
        }

        if(!preg_match("/^[0-9]{9,}+$/", $request->get('phone'))) {
            throw new \Exception('Wrong phone format: Please check if the phone number is on the format: 1234657890');
        }
    }

    /**
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function sendMessage(Request $request)
    {
        $this->validateNumber($request);
        $this->client->messages->create(
            '+' . $request->get('phone'),
            [
                'from' => env('TWILIO_NUMBER'),
                'body' => $request->get('message')
            ]
        );
    }
}
