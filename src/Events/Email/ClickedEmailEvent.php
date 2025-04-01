<?php

namespace HenryAvila\EmailTracking\Events\Email;

use HenryAvila\EmailTracking\Contracts\HasEnvelopeAndMessage;
use HenryAvila\EmailTracking\DataObjects\Mailgun\ClientInfo;
use HenryAvila\EmailTracking\DataObjects\Mailgun\Geolocation;
use HenryAvila\EmailTracking\Traits\HasEnvelopeAndMessageTrait;

class ClickedEmailEvent extends AbstractEmailEvent implements HasEnvelopeAndMessage
{
    use HasEnvelopeAndMessageTrait;
    const CODE = 'clicked';
    public string $ip;
    public string $url;

    public ClientInfo $clientInfo;

    public Geolocation $geolocation;

    public function __construct(array $payload)
    {
        parent::__construct($payload);

        $this->initializeEnvelopeAndMessage($payload);

        $this->ip = $payload['ip'] ?? '';
        $this->url = $payload['url']  ?? '';

        $this->clientInfo = new ClientInfo($payload['client-info']);
        $this->geolocation = new Geolocation($payload['geolocation']);
    }
}