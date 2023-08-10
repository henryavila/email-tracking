<?php

namespace HenryAvila\EmailTracking\Models;

use HenryAvila\EmailTracking\Traits\ModelWithEmailsSenderTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    use ModelWithEmailsSenderTrait;

    protected $guarded = [];
}
