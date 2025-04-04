<?php

declare(strict_types=1);

namespace HenryAvila\EmailTracking\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailEventLog extends Model
{
    protected $guarded = [];

    public function payload(): Attribute
    {
        return new Attribute(
            get: fn ($value) => json_decode($value, true)
        );
    }

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }
}
