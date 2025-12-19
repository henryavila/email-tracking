<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string|null $email_type Email Type (optional, for categorization)
 * @property string $message_id
 * @property string $subject
 * @property string $to Destinatário
 * @property string $cc Com Cópia
 * @property string $bcc Com Cópia Oculta
 * @property string $reply_to Responder para
 * @property int $delivery_status_attempts
 * @property string $delivery_status_message
 * @property int $sender_id
 * @property string $sender_type
 * @property Carbon $delivered_at
 * @property Carbon $failed_at
 * @property Carbon $first_opened_at
 * @property Carbon $last_opened_at
 * @property Carbon $first_clicked_at
 * @property Carbon $last_clicked_at
 * @property int $opened
 * @property int $clicked
 * @property string $body_html
 * @property string $body_txt
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property mixed $sender
 */
class Email extends Model
{
    protected $fillable = [
        'message_id',
        'sender_type',
        'sender_id',
        'subject',
        'email_type',
        'to',
        'cc',
        'bcc',
        'reply_to',
        'delivered_at',
        'failed_at',
        'opened',
        'clicked',
        'delivery_status_attempts',
        'delivery_status_message',
        'last_opened_at',
        'last_clicked_at',
        'first_opened_at',
        'first_clicked_at',
        'body_html',
        'body_txt',
    ];

    protected $casts = [
        'message_id' => 'string',
        'delivery_status_attempts' => 'int',
        'sender_id' => 'int',
        'opened' => 'int',
        'clicked' => 'int',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'first_opened_at' => 'datetime',
        'first_clicked_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (config('email-tracking.email-db-connection') !== null) {
            $this->setConnection(config('email-tracking.email-db-connection'));
        }
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function emailEventLogs(): HasMany
    {
        return $this->hasMany(EmailEventLog::class);
    }
}
