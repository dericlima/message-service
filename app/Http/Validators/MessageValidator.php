<?php

namespace App\Http\Validators;

use Illuminate\Http\Request;

class MessageValidator
{
    const AVAILABLE_SERVICES = [
        'sms',
        'email',
        'push'
    ];

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function validate(Request $request)
    {
        $this->validateParameters($request);

        $this->validateService($request);

        $this->validateSize($request->get('message'));
    }

    /**
     * @throws \Exception
     */
    private function validateParameters(Request $request)
    {
        if (!$request->has('message')) {
            throw new \Exception('Missing required parameter: message');
        }
    }

    /**
     * @throws \Exception
     */
    private function validateService(Request $request)
    {
        if (!$request->has('services')) {
            throw new \Exception('Missing required parameter: services');
        }

        $services = $request->get('services');
        if (!is_array($services)) {
            throw new \Exception('Parameter services must be an array, please check if the parameters is on the format: services[]=sms');
        }

        foreach ($services as $service) {
            if (!in_array($service, self::AVAILABLE_SERVICES)) {
                throw new \Exception('Unrecognized service: ' . $service);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function validateSize(string $message)
    {
        if (strlen($message) > 150) {
            throw new \Exception('Message can not contain more than 150 characters');
        }
    }
}
