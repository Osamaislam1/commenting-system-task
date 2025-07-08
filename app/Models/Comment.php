<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'post_id', 'parent_comment_id', 'depth'];


    // get the post that this comment belongs to
    public function post()
    {
        return $this->belongsTo(Post::class);
    }


    // get the replies for this comment
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }


    //  get the parent comment,if any
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    //check if this comment can have replies

    public function canReply($maxDepth = 3)
    {
        return $this->depth < $maxDepth;
    }
}
