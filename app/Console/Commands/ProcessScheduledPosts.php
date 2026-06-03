<?php

// Register in routes/console.php: Schedule::command('publishin:process-scheduled')->everyMinute();

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPost;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessScheduledPosts extends Command
{
    protected $signature = 'publishin:process-scheduled';
    protected $description = 'Dispatch publishing jobs for posts scheduled to publish now';

    public function handle(): int
    {
        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_at', '<=', Carbon::now())
            ->whereNull('published_at')
            ->with('versions')
            ->get();

        foreach ($posts as $post) {
            PublishScheduledPost::dispatch($post)->onQueue('publishing');
            $this->info("Dispatching post {$post->id}: {$post->title}");
        }

        $this->info("Dispatched {$posts->count()} posts for publishing.");

        return Command::SUCCESS;
    }
}
