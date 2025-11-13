<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'post_title', 'slug', 'post_content', 'post_excerpt', 'post_status', 'post_parent', 'post_type', 'comment_status', 'menu_order', 'post_password'];
}
