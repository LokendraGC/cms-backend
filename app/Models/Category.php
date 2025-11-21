<?php

namespace App\Models;

use App\Traits\HasPosition;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasPosition;

    protected $fillable = ['name', 'slug', 'type', 'description', 'parent', 'parent_id_backup', 'menu_order'];

    public function categoryMeta()
    {
        return $this->hasMany(CategoryMeta::class);
    }

    public function position(): MorphOne
    {
        return $this->morphOne(Position::class, 'positionable');
    }
}
