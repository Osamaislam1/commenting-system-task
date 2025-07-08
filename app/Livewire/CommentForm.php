<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\Comment;
use Livewire\Attributes\Validate;

class CommentForm extends Component
{
    public Post $post;
    public $parentComment = null;
    public $depth = 1;
    public $maxDepth = 3;

    #[Validate('nullable|string|min:1')]
    public $content = '';

    public function mount(Post $post, $parentComment = null, $depth = 1, $maxDepth = 3)
    {
        $this->post = $post;
        $this->parentComment = $parentComment;
        $this->depth = $depth;
        $this->maxDepth = $maxDepth;
    }

    public function submit()
    {
        $this->validate();

        if ($this->depth > $this->maxDepth) {
            session()->flash('error', 'Maximum reply depth reached.');
            return;
        }


        Comment::create([
            'content' => $this->content,
            'post_id' => $this->post->id,
            'parent_comment_id' => $this->parentComment ? $this->parentComment->id : null,
            'depth' => $this->depth,
        ]);

        $this->reset('content');
        $this->dispatch('commentAdded');
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
