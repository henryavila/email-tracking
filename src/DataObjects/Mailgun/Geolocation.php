<?php

namespace HenryAvila\EmailTracking\DataObjects\Mailgun;

class Geolocation
{
    public ?string $city;
    public ?string $country;
    public ?string $region;
    public ?string $timezone;

    public function __construct(array $payload)
    {
        $this->city = $payload['city'] ?? null;
        $this->country = $payload['country'] ?? null;
        $this->region = $payload['region'] ?? null;
        $this->timezone = $payload['timezone'] ?? null;
    }

}