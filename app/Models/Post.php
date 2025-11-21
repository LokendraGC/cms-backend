<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PostMeta;
use App\Models\User;
use App\Models\Comment;
use App\Traits\HasPosition;

class Post extends Model
{
    use HasFactory, SoftDeletes,HasPosition;

    protected $fillable = ['user_id', 'post_title', 'slug', 'post_content', 'post_excerpt', 'post_status', 'post_parent', 'post_type', 'comment_status', 'menu_order', 'post_password'];

    public function postMeta()
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
