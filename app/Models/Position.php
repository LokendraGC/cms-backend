<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Position extends Model
{
    protected $fillable = [
        'positionable_id',
        'positionable_type',
        'position'
    ];

    public function positionable(): MorphTo
    {
        return $this->morphTo();
    }
}
