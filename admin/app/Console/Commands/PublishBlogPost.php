<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use Illuminate\Console\Command;

class PublishBlogPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-blog-post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = BlogPost::where('published_at', '<=', now())
            ->where(['is_published' => false])
            ->orderByDesc('published_at')
            ->get();
        foreach ($posts as $post) {
            $post->is_published = true;
            $post->created_at = $post->published_at;
            $post->published_at = null;
            $post->save();
            $this->comment('Public post: ' . $post->name);
        }
    }
}
