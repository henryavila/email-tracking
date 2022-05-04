Rastreamento de e-mail Integrado ao Laravel Nova


## Mailgun configuration
On mailgun interface, add a `webhook` to the url `APP_URL/webhooks/mailgun`


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
composer require apps-inteligentes/email-tracking
```

Publish and run the migrations with:

```bash
php artisan vendor:publish --tag="email-tracking-migrations"
php artisan migrate
```

Publish the lang files (optional) with:

```bash
php artisan vendor:publish --tag="email-tracking-translations"
```


---

## Configuration

On `NovaServiceProvider.php`, add the code:
```php
    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        \AppsInteligentes\EmailTracking\Nova\EmailTrackingTool::make()
    }
```
This will display the e-mails on Laravel Nova Dashboard.

If you need to customize the Nova Resource, just create a new one extendind `AppsInteligentes\EmailTracking\Nova\EmailResource` and use this code
```php
    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {                    
        \AppsInteligentes\EmailTracking\Nova\EmailTrackingTool::make()
            ->emailResource(CustomEmailResource::class)                        
    }                
```

---


On all models that can send e-mail, and add the trait `ModelWithEmailsSenderTrait`



On `EventServiceProvider.php`, add the code
```php
   /**
     * The event listener mappings for the application.
     *
     * @var array
     */
   protected $listen = [
        \Illuminate\Mail\Events\MessageSent::class => [
            \AppsInteligentes\EmailTracking\Listeners\LogEmailSentListener::class,
        ],
   ];
```

At this point, all e-mail sent from app, will be logged on the app, but the sender will not be saved


## Save the Email sender
To be able to track the e-mail sender, you must create a custom `Mailable` or `Notification`. the default mail can't define the sender (like Nova Reset password e-mail)

### Mailable
When creating a new Mailable, overwrite the Base Mailable Class with `AppsInteligentes\EmailTracking\Mail\TrackableMail`

This default code:
```php
class SampleMail extends \Illuminate\Mail\Mailable
{
	public function build()
	{
		return $this->view('emails.sample');
	}
}
```

must be overwritten by this one:
```php
class SampleMail extends \AppsInteligentes\EmailTracking\Mail\TrackableMail
{
    public function __construct(public \Illuminate\Database\Eloquent\Model $model)
    {
        parent::__construct($model, 'emails.sample');
    }

    public function build(): void
    {
        parent::build();
        
        // Finish the E-mail buildUp. The view was already defined
    }
}
```
**Ps:** In this sample, `'emails.sample'` is the name of the view generated for this sample. Overwrite it with it with yours. 

This new code will pass in the constructor the model that is the email sender. 
At this point, in `build()` method, you can continue to setup the mailable, but know that the view is already defined. 
If you call the view method again, the sender configuration will be overwritten.

To send the Mailable, just pass the model in the mailable constructor 
```php
// example: Send the Sample Mail to User with id 1
$user = User::find(1);
Mail::to($user)->send(new App\Mail\SampleMail($user));
```


### Notification
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
public function __construct(public \Illuminate\Database\Eloquent\Model $model)
{
    //
}

public function toMail($notifiable): MailMessage
{
    return (new \AppsInteligentes\EmailTracking\Notifications\TrackableNotificationMailMessage($this->model))
        ->line('The introduction to the notification.')
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

## Displaying the e-mails from sender
To be able to display the e-mails sent from a send, add this code in the `fields()` method on nova resource
```php
public function fields(Request $request)
{
    return [
        ...
        \AppsInteligentes\EmailTracking\EmailTracking::hasManyEmailsField(),
        ...
    ];
}
```

---

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
