<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Mailgun\Mailgun;

class MailGunMessageProvider implements MessageProvider
{
    const REQUIRED_PARAMS = ['email', 'subject'];

    /** @var string */
    private $key;

    /** @var string */
    private $domain;

    /** @var Mailgun */
    private Mailgun $client;

    public function __construct()
    {
        $this->key      = env('MAILGUN_KEY');
        $this->domain   = env('MAILGUN_DOMAIN');
        $this->client   = Mailgun::create($this->key);
    }

    /**
     * @throws \Exception
     */
    public function validateMailParameters(Request $request)
    {
        foreach (self::REQUIRED_PARAMS as $param) {
            if (!$request->has($param)) {
                throw new \Exception('Missing required parameter: ' . $param);
            }
        }

        if (!filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid e-mail format');
        }
    }

    /**
     * @throws \Exception
     */
    public function sendMessage(Request $request)
    {
        $this->validateMailParameters($request);
        $this->client->messages()->send($this->domain, [
            'from'	    => 'transfergo@' . $this->domain,
            'to'	    => $request->get('email'),
            'subject'   => $request->get('subject'),
            'text'	    => $request->get('message')
        ]);
    }
}
