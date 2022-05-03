<?php

namespace AppsInteligentes\EmailTracking\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrackableMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Model $model, public string $viewName)
    {

    }

    public function build(): void
    {
        $this->view(
            $this->viewName, [
                // The $model variable will be used in VIEW and in LogEmailSentListener class
                'model' => $this->model
            ]
        );
    }
}
