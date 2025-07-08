<li class="mb-3">
    <div class="card border-0 shadow-sm bg-white rounded-4">
        <div class="card-body pb-2">
            <div class="d-flex align-items-center mb-2">
                <span class="me-2"><i class="bi bi-person-circle fs-4 text-secondary"></i></span>
                <div class="flex-grow-1">
                    <strong>Comment</strong>
                    <span class="badge bg-light text-dark ms-2">Depth: {{ $depth }}</span>
                </div>
            </div>
            <div class="mb-2 fs-6">{{ $comment->content }}</div>
            <div class="mb-2">
                @if ($depth < $maxDepth)
                    @if ($this->replyingTo === $comment->id)
                        <button wire:click="hideReplyForm" class="btn btn-outline-secondary btn-sm me-2">Cancel</button>
                    @else
                        <button wire:click="showReplyForm({{ $comment->id }})" class="btn btn-outline-primary btn-sm">Reply</button>
                    @endif
                @endif
            </div>
            @if ($this->replyingTo === $comment->id)
                <div class="mb-2">
                    @livewire('comment-form', ['post' => $post, 'parentComment' => $comment, 'depth' => $depth + 1, 'maxDepth' => $maxDepth], key('reply-'.$comment->id))
                </div>
            @endif
        </div>
    </div>
    @if ($comment->replies && $comment->replies->count())
        <ul class="list-unstyled ms-4 mt-2">
            @foreach ($comment->replies as $reply)
                @include('livewire.partials.comment', ['comment' => $reply, 'depth' => $depth + 1, 'maxDepth' => $maxDepth, 'post' => $post])
            @endforeach
        </ul>
    @endif
</li>
