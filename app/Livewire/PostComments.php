<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\Comment;

class PostComments extends Component
{
    public Post $post;
    public $maxDepth = 3;
    public $replyingTo = null;

    protected $listeners = ['commentAdded' => '$refresh'];

    public function mount(Post $post)
    {
        $this->post = $post;
    }


    // recursively load comments and their replies
    public function getCommentsTree($comments)
    {
        return $comments->map(function ($comment) {
            $comment->setRelation('replies', $comment->replies()->get());
            if ($comment->replies->isNotEmpty()) {
                $comment->replies = $this->getCommentsTree($comment->replies);
            }
            return $comment;
        });
    }


    public function showReplyForm($commentId)
    {
        $this->replyingTo = $commentId;
    }

    //hide the reply form
    public function hideReplyForm()
    {
        $this->replyingTo = null;
    }

    public function render()
    {
        $comments = $this->post->comments()->with('replies')->get();
        $commentsTree = $this->getCommentsTree($comments);
        return view('livewire.post-comments', [
            'comments' => $commentsTree,
            'post' => $this->post,
            'maxDepth' => $this->maxDepth,
        ]);
    }
}
