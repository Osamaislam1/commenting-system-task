<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Posts</h2>
        <button class="btn btn-success" wire:click="showCreateForm">Create Post</button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($showForm)
        <div class="card mb-4">
            <div class="card-body">
                <form wire:submit.prevent="{{ $isEdit ? 'updatePost' : 'createPost' }}">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" id="title" class="form-control" wire:model.defer="title" placeholder="Enter post title">
                        @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea id="content" class="form-control" wire:model.defer="content" rows="3" placeholder="Enter post content"></textarea>
                        @error('content') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Create' }}</button>
                        <button type="button" class="btn btn-secondary" wire:click="resetForm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $post->title }}</td>
                        <td class="text-truncate" style="max-width: 300px;">{{ $post->content }}</td>
                        <td>
                            <a href="{{ url('/post/'.$post->id) }}" wire:navigate class="btn btn-outline-info btn-sm" target="_self">View</a>
                            <button class="btn btn-outline-primary btn-sm" wire:click="showEditForm({{ $post->id }})">Edit</button>
                            <button class="btn btn-outline-danger btn-sm" wire:click="confirmDelete({{ $post->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>
        {{ $posts->links() }}
    </div>

    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this post?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteConfirmed">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
