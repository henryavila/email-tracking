<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class TrackableMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public $model, public string $viewName, public $viewData = []) {}

    public function content(): Content
    {
        $this->viewData['model'] = $this->model;

        return new Content(
            view: $this->viewName,
            with: $this->viewData
        );
    }

    /**
     * Build the view data array.
     * Adds email_type to the data if the mailable implements getEmailType() method.
     *
     * This allows mailables to categorize emails by implementing a getEmailType() method
     * that returns a string or BackedEnum value. The email type will be automatically
     * captured and stored in the emails table for filtering and analytics.
     *
     * @return array View data with email_type included if available
     */
    public function buildViewData(): array
    {
        $data = parent::buildViewData();

        if (method_exists($this, 'getEmailType')) {
            $emailType = $this->getEmailType();
            $data['__email_type'] = $emailType instanceof \BackedEnum ? $emailType->value : $emailType;
        }

        return $data;
    }
}
