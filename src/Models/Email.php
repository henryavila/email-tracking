<?php

namespace AppsInteligentes\EmailTracking\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string message_id
 * @property string subject
 * @property string to Destinatário
 * @property string cc Com Cópia
 * @property string bcc Com Cópia Oculta
 * @property string reply_to Responder para
 * @property int delivery_status_attempts
 * @property string delivery_status_message
 * @property int sender_id
 * @property string sender_type
 * @property Carbon delivered_at
 * @property Carbon failed_at
 * @property Carbon first_opened_at
 * @property Carbon last_opened_at
 * @property Carbon first_clicked_at
 * @property Carbon last_clicked_at
 * @property int opened
 * @property int clicked
 *
 * @property string body_html
 * @property string body_txt
 *
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 *
 * @property mixed sender
 */
class Email extends Model
{
    protected $dates = [
        'delivered_at',
        'failed_at',
        'last_opened_at',
        'last_clicked_at',
        'first_opened_at',
        'first_clicked_at',
    ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'message_id'               => 'string',
        'delivery_status_attempts' => 'int',
        'sender_id'                => 'int',
        'opened'                   => 'int',
        'clicked'                  => 'int',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (config('email-tracking.email-db-connection') !== null) {
            $this->setConnection(config('email-tracking.email-db-connection'));
        }
    }

    public function sender(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
