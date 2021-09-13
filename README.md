# TransferGO Message Service

Service to send messages to customer from different channels.
The service must be able to send the same message on multiple channels, supporting a fail-over feature in case one 
of the channels fail.

## Setting up

Clone the project: `https://github.com/dericlima/message-service.git`

Install all packages: `composer install`

Add the keys shared on slack in the `.env` file

Config the database
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=XXX
DB_USERNAME=XXX
DB_PASSWORD=XXX
```

Run all migrations: `php artisan migrate`

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

# Improvements

This is a list of improvement that can be done in the future:

* Improve error handling using Exception types and the `Handler`
* Push the service to the cloud (Heroku) to be tested easily
* Add a third service
* Use the `Lang` facade in all strings
* Improve the track system to show better messages to the user, for now we only show the entire record saved in the table
