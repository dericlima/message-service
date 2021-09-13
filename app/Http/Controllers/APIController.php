<?php

namespace App\Http\Controllers;

use App\Http\Services\MailGunMessageProvider;
use App\Http\Services\TwilioMessageProvider;
use App\Http\Validators\MessageValidator;
use Illuminate\Http\JsonResponse;
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
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $this->validator->validate($request);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }

        $result = [];
        foreach ($request->get('services') as $service) {
            switch ($service) {
                case 'sms':
                    $result = $this->sendSMSMessage($request);
                    break;
                case 'email':
                    $result = $this->sendEmailMessage($request);
                    break;
            }
        }

        return response()->json($result);
    }

    private function sendSMSMessage(Request $request): array
    {
        try {
            $messageProvider = new TwilioMessageProvider();
            $messageProvider->sendMessage($request);
            $result =[
                'service'   => 'SMS',
                'success'   => 'SMS sent with success'
            ];
        } catch (\Exception $exception) {
            $result =[
                'service'   => 'SMS',
                'error'     => $exception->getMessage()
            ];
        }

        return $result;
    }

    private function sendEmailMessage(Request $request): array
    {
        try {
            $messageProvider = new MailGunMessageProvider();
            $messageProvider->sendMessage($request);
            $result =[
                'service'   => 'Email',
                'success'   => 'Email sent with success'
            ];
        } catch (\Exception $exception) {
            $result =[
                'service'   => 'Email',
                'error'     => $exception->getMessage()
            ];
        }

        return $result;
    }
}
