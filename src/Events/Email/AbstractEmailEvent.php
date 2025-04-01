<?php

namespace HenryAvila\EmailTracking\Events\Email;

class AbstractEmailEvent
{
    public string $timestamp;

    public string $id;

    public function __construct(public readonly array $payload)
    {
        $this->timestamp = (string)$payload['timestamp'];
        $this->id = $payload['id'];
    }
}