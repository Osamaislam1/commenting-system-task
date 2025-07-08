<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Laravel is working!';
});

Route::get('/post/{post}', function (Post $post) {
    return view('post', compact('post'));
});
