<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content'];


    //get the top-level comments for the post
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_comment_id');
    }
}
