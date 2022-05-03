<?php

namespace AppsInteligentes\EmailTracking\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface ModelWithEmails
{
    /**
     * A Morph relation with emails
     * @return MorphMany
     */
    public function emails(): MorphMany;
}
