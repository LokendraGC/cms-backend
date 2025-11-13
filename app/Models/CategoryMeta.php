<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMeta extends Model
{
    use HasFactory;

    protected $table = 'category_metas';

    protected $fillable = ['category_id', 'meta_key', 'meta_value'];

    public $timestamps = false;
}
