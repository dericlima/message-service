<?php

namespace App\Http\Controllers;

use App\Http\Services\MailGunMessageProvider;
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

        $result = [];
        foreach ($request->get('services') as $service) {
            switch ($service) {
                case 'sms':
                    $result[] = $this->sendSMSMessage($request);
                    break;
                case 'email':
                    $result[] = $this->sendEmailMessage($request);
                    break;
            }
        }

        return $result;
    }

    private function sendSMSMessage(Request $request): array
    {
        $result = [];
        try {
            $messageProvider = new TwilioMessageProvider();
            $messageProvider->sendMessage($request);
            $result[] =[
                'Service'   => 'SMS',
                'Success'   => 'SMS sent with success'
            ];
        } catch (\Exception $exception) {
            $result[] =[
                'Service'       => 'SMS',
                'Request error' => $exception->getMessage()
            ];
        }

        return $result;
    }

    private function sendEmailMessage(Request $request): array
    {
        $result = [];
        try {
            $messageProvider = new MailGunMessageProvider();
            $messageProvider->sendMessage($request);
            $result[] =[
                'Service'   => 'Email',
                'Success'   => 'Email sent with success'
            ];
        } catch (\Exception $exception) {
            $request[] =[
                'Service'       => 'Email',
                'Request error' => $exception->getMessage()
            ];
        }

        return $result;
    }
}
