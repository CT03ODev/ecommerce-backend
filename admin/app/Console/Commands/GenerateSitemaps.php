<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemaps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap xml';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Generating sitemaps...');
        $sitemapInstance = Sitemap::create();

        $staticPages = [
            route('home'),
            route('blog.index'),
        ];
        $categories = BlogCategory::where(['is_published' => 1])->get();
        $posts = BlogPost::where(['is_published' => 1])->get();
        $tags = Tag::where([])->get();

        foreach ($staticPages as $page) {
            $sitemapInstance->add(Url::create($page)
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8)
            );
        }

        foreach ($categories as $category) {
            $sitemapInstance->add(Url::create($category->url)
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8)
            );
        }

        foreach ($posts as $post) {
            $sitemapInstance->add(Url::create($post->url)
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.8)
            );
        }

        foreach ($tags as $tag) {
            $sitemapInstance->add(Url::create($tag->url)
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.1)
            );
        }

        $sitemapInstance->writeToDisk('public', 'sitemap.xml');
        $this->comment('Generated sitemaps success!');
    }
}
