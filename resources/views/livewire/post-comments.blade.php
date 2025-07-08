<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h3 class="card-title mb-4 text-primary">Comments </h3>
        <div class="mb-4 border-bottom pb-3">
            @livewire('comment-form', ['post' => $post, 'parentComment' => null, 'depth' => 1, 'maxDepth' => $maxDepth])
        </div>
        <ul class="list-unstyled">
            @foreach ($comments as $comment)
                @include('livewire.partials.comment', ['comment' => $comment, 'depth' => 1, 'maxDepth' => $maxDepth, 'post' => $post])
            @endforeach
        </ul>
    </div>
</div>
