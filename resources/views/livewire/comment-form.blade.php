<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex align-items-start mb-2">
            <span class="me-2"><i class="bi bi-person-circle fs-3 text-primary"></i></span>
            <form wire:submit.prevent="submit" class="flex-grow-1">
                @if (session()->has('error'))
                    <div class="alert alert-danger py-1 px-2 mb-2">{{ session('error') }}</div>
                @endif
                <div class="mb-2">
                    <textarea wire:model.defer="content" class="form-control rounded-3" rows="2" placeholder="Write a comment..." aria-label="Write a comment" @if($depth > $maxDepth) disabled @endif></textarea>
                    @error('content') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="btn btn-success btn-sm px-4" @if($depth > $maxDepth) disabled @endif>Submit</button>
            </form>
        </div>
    </div>
</div>
