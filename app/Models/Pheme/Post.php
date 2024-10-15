<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['body', 'profiles_id', 'thread_id'];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profiles_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}






/*
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use TeamTeaTime\Forum\Models\Traits\HasAuthor;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class Post extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $connection = 'pheme';
    protected $table = 'forum_posts';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'profiles_id',
        'threads_id',
        'content',
        'sequence',
        'updated_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.general.pagination.posts');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class)->withTrashed();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Post::class, 'post_id')->withTrashed();
    }

    public function scopeRecent(Builder $query): Builder
    {
        $age = strtotime(config('forum.general.old_thread_threshold'), 0);
        $cutoff = time() - $age;

        return $query->where('updated_at', '>', date('Y-m-d H:i:s', $cutoff))->orderBy('updated_at', 'desc');
    }

    public function getSequenceNumber(bool $withTrashed = false): int
    {
        $posts = $withTrashed
            ? $this->thread->posts()->withTrashed()->get()
            : $this->thread->posts;

        foreach ($posts as $index => $post) {
            if ($post->id == $this->id) {
                return $index + 1;
            }
        }
    }

    public function getPage(): int
    {
        return ceil($this->getSequenceNumber(true) / $this->getPerPage());
    }

    protected function route(): Attribute
    {
        return new Attribute(
            get: fn () => Forum::route('thread.show', $this),
        );
    }
}
