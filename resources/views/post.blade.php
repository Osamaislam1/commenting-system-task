@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h1 class="card-title display-5 mb-3">{{ $post->title }}</h1>
                    <p class="card-text fs-5 text-secondary">{{ $post->content }}</p>
                    <a href="/" wire:navigate class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-arrow-left"></i> Back to Home</a>
                </div>
            </div>
            @livewire('post-comments', ['post' => $post])
        </div>
    </div>
</div>
@endsection
