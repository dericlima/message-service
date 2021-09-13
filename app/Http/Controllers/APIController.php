<?php

namespace App\Http\Controllers;

use App\Http\Services\MailGunMessageProvider;
use App\Http\Services\TwilioMessageProvider;
use App\Http\Validators\MessageValidator;
use App\Models\Tracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class APIController extends Controller
{
    const SERVICE_TYPE_SMS      = 'sms';
    const SERVICE_TYPE_EMAIL    = 'email';

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

            $tracker            = new Tracker();
            $tracker->service   = self::SERVICE_TYPE_SMS;
            $tracker->code      = Str::random();
            $tracker->message   = $request->get('message');
            $tracker->payload   = ['phone' => $request->get('phone')];
            $tracker->save();

            $result =[
                'service'   => 'SMS',
                'success'   => 'Message sent successfully! Your tracking code: ' . $tracker->code
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

            $tracker            = new Tracker();
            $tracker->service   = self::SERVICE_TYPE_EMAIL;
            $tracker->code      = Str::random();
            $tracker->message   = $request->get('message');
            $tracker->payload   = [
                'subject'   => $request->get('subject'),
                'email'     => $request->get('email'),
            ];
            $tracker->save();

            $result =[
                'service'   => 'SMS',
                'success'   => 'Message sent successfully! Your tracking code: ' . $tracker->code
            ];
        } catch (\Exception $exception) {
            $result =[
                'service'   => 'Email',
                'error'     => $exception->getMessage()
            ];
        }

        return $result;
    }

    public function trackCode(Request $request)
    {
        try {
            $this->validator->validateCode($request);

            $tracker = Tracker::where('code', $request->get('code'))->first();
            if ($tracker) {
                return response()->json([
                    'success' => 'Code found!',
                    'data' => $tracker->toArray(),
                ]);
            } else {
                return response()->json(['error' => 'Code not found']);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
    }
}
