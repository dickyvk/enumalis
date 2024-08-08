<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;

class PhemeSeeder extends Seeder
{
    private static $PHEME_ROW = 20;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(self::$PHEME_ROW)->create();
        Thread::factory(self::$PHEME_ROW)->create();
        Post::factory(self::$PHEME_ROW)->create();

        foreach(Category::get() as $category)
        {
            $post_count = 0;
            foreach(Thread::where('categories_id', $category->id)->orderBy('created_at')->orderBy('id')->get() as $thread)
            {
                $sequence = 0;
                foreach(Post::where('threads_id', $thread->id)->orderBy('created_at')->orderBy('id')->get() as $post)
                {
                    $post->sequence = $sequence++;
                    $post->save();
                }
                if($sequence > 0)
                {
                    $thread->first_post_id = $thread->getFirstPostId();
                    $thread->last_post_id = $thread->getLastPostId();
                    $thread->reply_count = $sequence;
                    $thread->save();
                }

                $post_count += $sequence;
            }
            if($post_count > 0)
            {
                $category->newest_thread_id = $category->getNewestThreadId();
                $category->latest_active_thread_id = $category->getLatestActiveThreadId();
                $category->thread_count = $category->getThreadCount();
                $category->post_count = $post_count;
                $category->save();
            }
        }
    }
}
