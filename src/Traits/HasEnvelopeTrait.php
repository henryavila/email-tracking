<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Traits;

use HenryAvila\EmailTracking\DataObjects\Mailgun\Envelope;

trait HasEnvelopeTrait
{
    public Envelope $envelope;

    public array $campaigns = [];

    public array $tags = [];

    public function initializeEnvelope(array $payload): void
    {
        if (isset($payload['envelope'])) {
            $this->envelope = new Envelope($payload['envelope']);
        }

        $this->campaigns = $payload['campaigns'] ?? [];
        $this->tags = $payload['tags'] ?? [];
    }
}
