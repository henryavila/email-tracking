<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Traits;

use HenryAvila\EmailTracking\DataObjects\Mailgun\ClientInfo;
use HenryAvila\EmailTracking\DataObjects\Mailgun\Geolocation;

trait HasClientInfoTrait
{
    public ClientInfo $clientInfo;

    public Geolocation $geolocation;


    public function initializeClientInfo(array $payload): void
    {
        if (isset($payload['client-info'])) {
            $this->clientInfo = new ClientInfo($payload['client-info']);
        }

        if (isset($payload['geolocation'])) {
            $this->geolocation = new Geolocation($payload['geolocation']);
        }
    }
}
