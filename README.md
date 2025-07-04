# E-mail tracking with Laravel and Mailgun

## Abandon Laravel Nova
Since I've abandoned Laravel Nova in favor of Filament, This package will no longer add support to Laravel Nova.
The exact content of this package with Laravel Nova has been moved to a new package https://packagist.org/packages/henryavila/laravel-nova-email-tracking
If you are using Laravel Nova, please use this new package instead.

---
## Version 6.2.0 upgrade
A new migration has been added to track email events. If you are updating from a previous version, make sure to publish and run migrations again:

```bash
php artisan vendor:publish --tag="email-tracking-migrations"
php artisan migrate
```

--

## Mailgun configuration

On mailgun interface, add a `webhook` to the url `<APP_URL>/webhooks/mailgun`

## Installation

Setup Laravel Mail with mailgun at https://laravel.com/docs/master/mail#mailgun-driver

Define the environments variable in your `.env` file

```
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=yourdomain.com
MAILGUN_SECRET=key-99999999999999999999999999999999
```

Install the package via composer:

```bash
composer require henryavila/email-tracking
```

Publish and run the migrations with:

```bash
php artisan vendor:publish --tag="email-tracking-migrations"
php artisan migrate
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="email-tracking-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * if defined, the Email model will use this database connection.
     * This connection name must be defined in database.connections config file
     */
    'email-db-connection' => null,

    /**
     * Save the HTML Body of all sent messages
     */
    'log-body-html' => true,

    /**
     * Save the TXT Body of all sent messages
     */
    'log-body-txt' => true,
];

```

---

## Configuration

On all models that can send e-mail, add the trait `ModelWithEmailsSenderTrait`


For Laravel 10, add this conde in `EventServiceProvider.php` file
```php
   protected $listen = [
        \Illuminate\Mail\Events\MessageSent::class => [
            \HenryAvila\EmailTracking\Listeners\LogEmailSentListener::class,
        ],
   ];
```

For Laravel 11, Add this code inside the `boot()` method of `AppServiceProvider.php`

```php
public function boot(): void
{
    // ...
    \Illuminate\Support\Facades\Event::listen(
        events: \Illuminate\Mail\Events\MessageSent::class,
        listener: \HenryAvila\EmailTracking\Listeners\LogEmailSentListener::class
    );
}
```


At this point, all e-mail sent from app, will be logged on the app, but the sender will not be saved

### Save the Email sender

To be able to track the e-mail sender, you must create a custom `Mailable` or `Notification`.

#### Mailable

When creating a new Mailable, it must extend the Class with `HenryAvila\EmailTracking\Mail\TrackableMail`

Also, You must change the constructor and content function.

This is the default mail class:
```php
class SampleMail extends \Illuminate\Mail\Mailable
{
    public function __construct()
    {
        //
    }

    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }	
}
```

Change the class to this:

```php
class SampleMail extends \HenryAvila\EmailTracking\Mail\TrackableMail
{
    public function __construct($modelSender)
    {
        $viewData = [];
        parent::__construct($modelSender, 'view.name', $viewData]);
    }
}
```

To send the Mailable, just pass the model in the mailable constructor

```php
// example: Send the Sample Mail to User with id 1
$user = User::find(1);
Mail::to($user)->send(new App\Mail\SampleMail($user));
```

#### Notification

When creating a notification, all you have to do is to change the `toMail()` method.
Replace the default code:

```php
public function toMail($notifiable): MailMessage
{
    return (new MailMessage)
        ->line('The introduction to the notification.')
        ->action('Notification Action', url('/'))
        ->line('Thank you for using our application!');
}
```

with this code:

```php
public function __construct(protected \Illuminate\Database\Eloquent\Model $model)
{
    //
}

public function toMail($notifiable): MailMessage
{
    return (new \HenryAvila\EmailTracking\Notifications\TrackableNotificationMailMessage($this->model))
        ->line('The introduction to the notification.')
        ->blankLine()
        ->line('Another line, after a blank line')
        ->htmlLine('Example of a <strong>HTML</strong> content')
        ->blankLineIf($condition)
        ->action('Notification Action', url('/'))
        ->line('Thank you for using our application!');
}
```

To send the notification

```php
// User with id 1 send the sample notification to multiple $clientes
$user = User::find(1);
Notification::send($clientes, new SampleNotification($user));
```

---

## Webhook Events

When a Mailgun webhook is successfully processed, the `EmailWebhookProcessed` event is automatically dispatched. This event contains the specific email event that was processed.

### Event Structure

```php
class EmailWebhookProcessed 
{
    public function __construct(public readonly AbstractEmailEvent $emailEvent)
    {
    }
}
```
### Implementing the Listener
To implement the listener, you can use the `EmailWebhookProcessed` event. This event contains the specific email event that was processed.

```php
<?php
declare(strict_types=1);

namespace App\Listeners;

use HenryAvila\EmailTracking\Events\Email\AbstractEmailEvent;
use HenryAvila\EmailTracking\Events\Email\AcceptedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\ClickedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;
use HenryAvila\EmailTracking\Events\Email\OpenedEmailEvent;
use HenryAvila\EmailTracking\Events\Email\PermanentFailureEmailEvent;
use HenryAvila\EmailTracking\Events\Email\SpamComplaintsEmailEvent;
use HenryAvila\EmailTracking\Events\Email\TemporaryFailureEmailEvent;
use HenryAvila\EmailTracking\Events\Email\UnsubscribeEmailEvent;
use HenryAvila\EmailTracking\Events\EmailWebhookProcessed;

class MailgunWebhookProcessedListener
{
    public function handle(EmailWebhookProcessed $event): void
    {
        match ($event->emailEvent::class) {
            AcceptedEmailEvent::class => $this->handleAcceptedEmailEvent($event->emailEvent),
            ClickedEmailEvent::class => $this->handleClickedEmailEvent($event->emailEvent),
            DeliveredEmailEvent::class => $this->handleDeliveredEmailEvent($event->emailEvent),
            OpenedEmailEvent::class => $this->handleOpenedEmailEvent($event->emailEvent),
            PermanentFailureEmailEvent::class => $this->handlePermanentFailureEmailEvent($event->emailEvent),
            TemporaryFailureEmailEvent::class => $this->handleTemporaryFailureEmailEvent($event->emailEvent),
            SpamComplaintsEmailEvent::class => $this->handleSpamComplaintsEmailEvent($event->emailEvent),
            UnsubscribeEmailEvent::class => $this->handleUnsubscribeEmailEvent($event->emailEvent),
        };
    }

    private function handleAcceptedEmailEvent(AcceptedEmailEvent $emailEvent)
    {
        // Implement your logic for handling accepted email events here
    }

    private function handleClickedEmailEvent(ClickedEmailEvent $emailEvent)
    {
        // Implement your logic for handling clicked email events here
    }

    private function handleDeliveredEmailEvent(DeliveredEmailEvent $emailEvent)
    {
        // Implement your logic for handling delivered email events here
    }

    private function handleOpenedEmailEvent(OpenedEmailEvent $emailEvent)
    {
        // Implement your logic for handling opened email events here
    }

    private function handlePermanentFailureEmailEvent(PermanentFailureEmailEvent $emailEvent)
    {
        // Implement your logic for handling permanent failure email events here
    }

    private function handleTemporaryFailureEmailEvent(TemporaryFailureEmailEvent $emailEvent)
    {
        // Implement your logic for handling temporary failure email events here
    }

    private function handleSpamComplaintsEmailEvent(SpamComplaintsEmailEvent $emailEvent)
    {
        // Implement your logic for handling spam complaints email events here
    }

    private function handleUnsubscribeEmailEvent(UnsubscribeEmailEvent $emailEvent)
    {
        // Implement your logic for handling unsubscribe email events here
    }
}
``` 

### Available Event Types

The EmailWebhookProcessed event wraps one of the following event types:

 - **AcceptedEmailEvent**: Email accepted for delivery
 - **ClickedEmailEvent**: Link clicked in email
 - **OpenedEmailEvent**: Email opened
 - **DeliveredEmailEvent**: Email successfully delivered
 - **PermanentFailureEmailEvent**: Permanent delivery failure
 - **TemporaryFailureEmailEvent**: Temporary delivery failure
 - **SpamComplaintsEmailEvent**: Spam complaint
 - **UnsubscribeEmailEvent**: Unsubscribe request

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Henry Ávila](https://github.com/henryavila)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
