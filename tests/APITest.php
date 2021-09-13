<?php

class APITest extends TestCase
{
    public function testMessageWithoutServices()
    {
        $this->get('/send_message?message=test');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Missing required parameter: services'
        );
    }

    public function testWrongServicesParameter()
    {
        $this->get('/send_message?message=test&services=sms');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Parameter services must be an array, please check if the parameters is on the format: services[]=sms'
        );
    }

    public function testUnknownServices()
    {
        $this->get('/send_message?message=test&services[]=unknown');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Unrecognized service: unknown'
        );
    }

    public function testSMSMissingPhoneNumber()
    {
        $this->get('/send_message?message=test&services[]=sms');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Missing required parameter: phone'
        );
    }

    public function testSMSPhoneWrongFormat()
    {
        $this->get('/send_message?message=test&services[]=sms&phone=wrong');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Wrong phone format: Please check if the phone number is on the format: 1234657890'
        );
    }

    public function testEmailMissingEmail()
    {
        $this->get('/send_message?message=test&services[]=email');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Missing required parameter: email'
        );
    }

    public function testEmailMissingSubject()
    {
        $this->get('/send_message?message=test&services[]=email&email=test@email.com');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Missing required parameter: subject'
        );
    }

    public function testCompleteEmailParametersMissingSMS()
    {
        $this->get('/send_message?message=test&services[]=email&services[]=sms&email=test@email.com');

        $response = $this->response->getOriginalContent();

        $this->assertEquals(
            $response['error'], 'Missing required parameter: phone'
        );
    }
}
