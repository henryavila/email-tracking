<?php

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

    public function __construct(public $model, public string $viewName, public $viewData = [])
    {
    }

    public function content(): Content
    {
        $this->viewData['model'] = $this->model;

        return new Content(
            view: $this->viewName,
            with: $this->viewData,
        );
    }
}
