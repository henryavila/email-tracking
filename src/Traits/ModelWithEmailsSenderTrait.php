<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Traits;

use HenryAvila\EmailTracking\Models\Email;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<Email> emails
 */
trait ModelWithEmailsSenderTrait
{
    public function emails(): MorphMany
    {
        return $this->morphMany(Email::class, 'sender');
    }
}
