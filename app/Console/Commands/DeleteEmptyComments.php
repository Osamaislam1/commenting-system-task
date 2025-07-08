<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;

class DeleteEmptyComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-empty-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete comments with empty content fields';


    public function handle()
    {
        // Delete comments where content is null or empty string
        $deleted = Comment::whereNull('content')->orWhere('content', '')->delete();
        $this->info("Deleted {$deleted} empty comments.");
    }
}
