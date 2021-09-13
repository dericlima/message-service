<?php

namespace App\Http\Controllers;

use App\Http\Services\TwilioMessageProvider;
use App\Http\Validators\MessageValidator;
use Illuminate\Http\Request;

class APIController extends Controller
{
    /** @var MessageValidator */
    private $validator;

    public function __construct()
    {
        $this->validator = new MessageValidator();
    }

    /**
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function sendMessage(Request $request): array
    {
        try {
            $this->validator->validate($request);
        } catch (\Exception $exception) {
            return ['Validation error' => $exception->getMessage()];
        }

        foreach ($request->get('services') as $service) {
            switch ($service) {
                case 'sms':
                    try {
                        $messageProvider = new TwilioMessageProvider();
                        $messageProvider->sendMessage($request);
                    } catch (\Exception $exception) {
                        return ['Request error' => $exception->getMessage()];
                    }

                    break;
            }
        }

        return $request->all();
    }
}
