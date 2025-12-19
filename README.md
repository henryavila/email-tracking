# Laravel Email Tracking with Mailgun

[![Latest Version on Packagist](https://img.shields.io/packagist/v/henryavila/email-tracking.svg?style=flat-square)](https://packagist.org/packages/henryavila/email-tracking)
[![Total Downloads](https://img.shields.io/packagist/dt/henryavila/email-tracking.svg?style=flat-square)](https://packagist.org/packages/henryavila/email-tracking)
[![License](https://img.shields.io/packagist/l/henryavila/email-tracking.svg?style=flat-square)](https://packagist.org/packages/henryavila/email-tracking)

Track email delivery, opens, clicks, and more using Mailgun webhooks. All data is stored in your database for easy querying and analytics.

## ✨ Features

- 📧 **Complete Email Tracking** - Track sent, delivered, opened, clicked, bounced, and failed emails
- 🔗 **Model Association** - Link emails to any Eloquent model (User, Order, Invoice, etc.)
- 🎯 **Email Categorization** - Classify emails by type (transactional, marketing, notifications, etc.)
- 📊 **Built-in Analytics** - Query delivery rates, open rates, click rates by email type
- 🪝 **Mailgun Webhooks** - Automatic event processing from Mailgun
- 🔒 **Secure Webhooks** - Signature verification for webhook security
- 💾 **Database Storage** - All email data stored in your database
- 🧪 **Fully Tested** - Comprehensive test suite included
- 📱 **Laravel 10+ & 11+** - Modern Laravel support

## 📋 Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- Mailgun account

## 📦 Installation

### 1. Install via Composer

```bash
composer require henryavila/email-tracking
```

### 2. Publish and Run Migrations

```bash
php artisan vendor:publish --tag="email-tracking-migrations"
php artisan migrate
```

### 3. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="email-tracking-config"
```

### 4. Configure Mailgun

Setup Laravel Mail with Mailgun driver. See [Laravel Mail Documentation](https://laravel.com/docs/master/mail#mailgun-driver).

Add to your `.env` file:

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=yourdomain.com
MAILGUN_SECRET=key-99999999999999999999999999999999
```

### 5. Setup Mailgun Webhook

In your Mailgun dashboard, add a webhook pointing to:

```
https://yourdomain.com/webhooks/mailgun
```

## ⚙️ Configuration

### Register Event Listener

The package needs to listen for sent emails to track them.

**Laravel 11+** (Recommended)

Add to `AppServiceProvider::boot()`:

```php
public function boot(): void
{
    \Illuminate\Support\Facades\Event::listen(
        events: \Illuminate\Mail\Events\MessageSent::class,
        listener: \HenryAvila\EmailTracking\Listeners\LogEmailSentListener::class
    );
}
```

**Laravel 10**

Add to `EventServiceProvider::$listen`:

```php
protected $listen = [
    \Illuminate\Mail\Events\MessageSent::class => [
        \HenryAvila\EmailTracking\Listeners\LogEmailSentListener::class,
    ],
];
```

### Configuration File

The published config file (`config/email-tracking.php`) allows customization:

```php
return [
    /**
     * Database connection for Email model (optional)
     * If null, uses default connection
     */
    'email-db-connection' => null,

    /**
     * Save HTML body of sent emails
     */
    'log-body-html' => true,

    /**
     * Save text body of sent emails
     */
    'log-body-txt' => true,
];
```

## 🚀 Usage

### Basic Mailable with Tracking

Extend `TrackableMail` instead of Laravel's `Mailable`:

```php
use HenryAvila\EmailTracking\Mail\TrackableMail;

class OrderShippedMail extends TrackableMail
{
    public function __construct($order)
    {
        $viewData = [
            'order' => $order,
            'trackingNumber' => $order->tracking_number,
        ];

        parent::__construct($order, 'emails.order-shipped', $viewData);
    }
}
```

Send the email:

```php
$order = Order::find(1);
Mail::to($order->customer)->send(new OrderShippedMail($order));
```

The email will be automatically tracked and linked to the `$order` model.

### Trackable Notifications

For notifications, use `TrackableNotificationMailMessage`:

```php
use HenryAvila\EmailTracking\Notifications\TrackableNotificationMailMessage;

class OrderShippedNotification extends Notification
{
    public function __construct(protected Order $order)
    {
    }

    public function toMail($notifiable): MailMessage
    {
        return (new TrackableNotificationMailMessage($this->order))
            ->subject('Your order has been shipped!')
            ->line('Your order #' . $this->order->number . ' is on its way.')
            ->action('Track Shipment', url('/orders/' . $this->order->id))
            ->line('Thank you for your purchase!');
    }
}
```

### Email Type Classification (v6.5.0+)

Categorize emails for better organization and analytics.

#### 1. Create Email Type Enum

```php
<?php

namespace App\Enums;

enum EmailType: string
{
    case TRANSACTIONAL = 'transactional';
    case MARKETING = 'marketing';
    case NOTIFICATION = 'notification';
    case ADMINISTRATIVE = 'administrative';
    case SYSTEM = 'system';
}
```

#### 2. Implement in Mailable

```php
use App\Enums\EmailType;
use HenryAvila\EmailTracking\Mail\TrackableMail;

class OrderConfirmationMail extends TrackableMail
{
    protected function getEmailType(): EmailType
    {
        return EmailType::TRANSACTIONAL;
    }
}
```

#### 3. Query by Type

```php
use App\Models\Email;

// Get all transactional emails
$transactional = Email::where('email_type', 'transactional')->get();

// Add convenient scopes to your Email model
Email::transactional()->delivered()->get();

// Analytics by type
$stats = Email::select('email_type')
    ->selectRaw('count(*) as total, sum(opened) as opens')
    ->groupBy('email_type')
    ->get();
```

**Learn more:** See [Email Type Classification Documentation](docs/email-type-classification.md) for complete guide with examples.

### Querying Emails

```php
use HenryAvila\EmailTracking\Models\Email;

// Get all emails for a model
$order = Order::find(1);
$emails = $order->emails; // Requires ModelWithEmailsSenderTrait on Order model

// Query email status
$delivered = Email::whereNotNull('delivered_at')->get();
$opened = Email::where('opened', '>', 0)->get();
$clicked = Email::where('clicked', '>', 0)->get();
$failed = Email::whereNotNull('failed_at')->get();

// Get recent emails
$recentEmails = Email::orderBy('created_at', 'desc')->limit(10)->get();

// Search by recipient
$userEmails = Email::where('to', 'like', '%user@example.com%')->get();
```

### Email Analytics

```php
// Delivery rate
$totalSent = Email::count();
$delivered = Email::whereNotNull('delivered_at')->count();
$deliveryRate = ($delivered / $totalSent) * 100;

// Open rate
$opened = Email::where('opened', '>', 0)->count();
$openRate = ($opened / $delivered) * 100;

// Click rate
$clicked = Email::where('clicked', '>', 0)->count();
$clickRate = ($clicked / $delivered) * 100;
```

## 🪝 Webhook Events

When Mailgun processes an email event (delivered, opened, clicked, etc.), the `EmailWebhookProcessed` event is dispatched.

### Listening to Webhook Events

Create a listener:

```php
<?php

namespace App\Listeners;

use HenryAvila\EmailTracking\Events\EmailWebhookProcessed;
use HenryAvila\EmailTracking\Events\Email\DeliveredEmailEvent;
use HenryAvila\EmailTracking\Events\Email\OpenedEmailEvent;

class MailgunWebhookProcessedListener
{
    public function handle(EmailWebhookProcessed $event): void
    {
        match ($event->emailEvent::class) {
            DeliveredEmailEvent::class => $this->handleDelivered($event->emailEvent),
            OpenedEmailEvent::class => $this->handleOpened($event->emailEvent),
            // Add other events as needed
            default => null,
        };
    }

    private function handleDelivered(DeliveredEmailEvent $event): void
    {
        // Your custom logic when email is delivered
        $email = $event->email;
        logger()->info("Email delivered to {$email->to}");
    }

    private function handleOpened(OpenedEmailEvent $event): void
    {
        // Your custom logic when email is opened
        $email = $event->email;
        logger()->info("Email opened by {$email->to}");
    }
}
```

Register the listener in `EventServiceProvider`:

```php
protected $listen = [
    \HenryAvila\EmailTracking\Events\EmailWebhookProcessed::class => [
        \App\Listeners\MailgunWebhookProcessedListener::class,
    ],
];
```

### Available Event Types

- `AcceptedEmailEvent` - Email accepted for delivery
- `DeliveredEmailEvent` - Email successfully delivered
- `OpenedEmailEvent` - Email opened by recipient
- `ClickedEmailEvent` - Link clicked in email
- `PermanentFailureEmailEvent` - Permanent delivery failure (bounce)
- `TemporaryFailureEmailEvent` - Temporary delivery issue
- `SpamComplaintsEmailEvent` - Marked as spam
- `UnsubscribeEmailEvent` - Unsubscribe request

## 🔧 Advanced Usage

### Model Association

Add the trait to models that send emails:

```php
use HenryAvila\EmailTracking\Traits\ModelWithEmailsSenderTrait;

class Order extends Model
{
    use ModelWithEmailsSenderTrait;
}
```

Now you can access emails:

```php
$order = Order::find(1);
$emails = $order->emails; // All emails sent for this order
```

### Custom Email Model

Extend the base Email model to add your own methods:

```php
namespace App\Models;

use App\Enums\EmailType;
use Illuminate\Database\Eloquent\Builder;

class Email extends \HenryAvila\EmailTracking\Models\Email
{
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'email_type' => EmailType::class,
        ]);
    }

    public function scopeTransactional(Builder $query): Builder
    {
        return $query->where('email_type', EmailType::TRANSACTIONAL);
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->whereNotNull('delivered_at');
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
```

Use your custom model by binding it in a service provider:

```php
$this->app->bind(
    \HenryAvila\EmailTracking\Models\Email::class,
    \App\Models\Email::class
);
```

## 📚 Documentation

- [Email Type Classification Guide](docs/email-type-classification.md) - Complete guide for email categorization
- [Changelog](CHANGELOG.md) - Version history and upgrade guides

## 🧪 Testing

```bash
composer test
```

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🤝 Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## 🔒 Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## 🙏 Credits

- [Henry Ávila](https://github.com/henryavila)
- [All Contributors](../../contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 💡 Upgrade Guides

### Upgrading to 6.5.0 from 6.4.x

Version 6.5.0 adds optional email type classification. **No breaking changes.**

**Optional:** To use email type classification:

```bash
# Publish new migration
php artisan vendor:publish --tag="email-tracking-migrations"
php artisan migrate

# Implement getEmailType() in your mailables
# See "Email Type Classification" section above
```

### Upgrading to 6.2.0 from earlier versions

A new migration was added to track email events.

```bash
php artisan vendor:publish --tag="email-tracking-migrations"
php artisan migrate
```

---

## 🆘 Support

- 📖 [Documentation](https://github.com/henryavila/email-tracking#readme)
- 🐛 [Issue Tracker](https://github.com/henryavila/email-tracking/issues)
- 💬 [Discussions](https://github.com/henryavila/email-tracking/discussions)

## ⭐ Show Your Support

Give a ⭐️ if this project helped you!
