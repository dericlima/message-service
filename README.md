# TransferGO Message Service

Service to send messages to customer from different channels

## Setting up

Clone the project: `https://github.com/dericlima/message-service.git`

Install all packages: `composer install`

Start the server: `php -S localhost:8000 -t public`

## API Calls

This first version supports two channels: SMS and Email
To call the API use the base URL:

`http://localhost:8000/send_message`

## Parameters

Each service has its own set of parameters. However both services share two common parameters: `message` and `services`.
Both are required parameters.

| Parameter    | Type | Example |
| :-------- | :------------ | :----------|
| message    | **required** | _send_message?message=test_ |
| services    | **required** | _send_message?message=test&services[]=sms_ |

Services is an array `services[]` of available services, multiple services can be configured with `send_message?services[]=sms&services[]=email`

## SMS Service Parameters

| Parameter    | Type | Example |
| :-------- | :------------ | :----------|
| phone    | **required** | _send_message?message=test&services[]=sms&phone=123456789_ |

## Email Service Parameters

| Parameter    | Type | Example |
| :-------- | :------------ | :----------|
| subject    | **required** | _send_message?message=test&services[]=email&subject=testSubject_ |
| email    | **required** | _send_message?message=test&services[]=email&email=test@test.com_ |

## Tests

To run all tests, from the root folder run: `vendor/phpunit/phpunit/phpunit`
