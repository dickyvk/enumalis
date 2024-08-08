<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use TeamTeaTime\Forum\Models\Traits\HasAuthor;
use TeamTeaTime\Forum\Support\Frontend\Forum;

class Thread extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $connection = 'pheme';
    protected $table = 'forum_threads';

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'profiles_id',
        'categories_id',
        'title',
        'pinned',
        'locked',
        'first_post_id',
        'last_post_id',
        'reply_count',
        'updated_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'threads_id');
    }

    public function firstPost(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'first_post_id');
    }

    public function lastPost(): HasOne
    {
        return $this->hasOne(Post::class, 'id', 'last_post_id');
    }

    public function getFirstPostId(): int
    {
        return $this->posts()->orderBy('created_at')->orderBy('id')->first()->id;
    }

    public function getLastPostId(): int
    {
        return $this->posts()->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first()->id;
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public const READERS_TABLE = 'forum_threads_read';

    public const STATUS_UNREAD = 'unread';
    public const STATUS_UPDATED = 'updated';

    private $currentReader = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->perPage = config('forum.general.pagination.threads');
    }

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(
            config('forum.integration.user_model'),
            self::READERS_TABLE,
            'thread_id',
            'user_id'
        )->withTimestamps();
    }

    public function scopeRecent(Builder $query): Builder
    {
        $age = strtotime(config('forum.general.old_thread_threshold'), 0);
        $cutoff = time() - $age;

        return $query->where('updated_at', '>', date('Y-m-d H:i:s', $cutoff))->orderBy('updated_at', 'desc');
    }

    public function scopeWithPostAndAuthorRelationships(Builder $query): Builder
    {
        return $query->with('firstPost', 'lastPost', 'firstPost.author', 'lastPost.author', 'lastPost.thread', 'author');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('pinned', 'desc')->orderBy('updated_at', 'desc');
    }

    public function getLastPost(): Post
    {
        return $this->posts()->orderBy('created_at', 'desc')->first();
    }

    public function markAsRead(Model $user): void
    {
        if ($this->isOld) {
            return;
        }

        if ($this->reader === null) {
            $this->readers()->attach($user->getKey());
        } elseif ($this->updatedSince($this->reader)) {
            $this->reader->touch();
        }
    }

    protected function route(): Attribute
    {
        return new Attribute(
            get: fn () => Forum::route('thread.show', $this),
        );
    }

    protected function isOld(): Attribute
    {
        return new Attribute(
            get: function ()
            {
                $age = config('forum.general.old_thread_threshold');
                return !$age || $this->updated_at->timestamp < (time() - strtotime($age, 0));
            }
        );
    }

    protected function reader(): Attribute
    {
        return new Attribute(
            get: function ()
            {
                if (!Auth::check()) {
                    return null;
                }

                if ($this->currentReader === null) {
                    $this->currentReader = $this->readers()->where('forum_threads_read.user_id', Auth::user()->getKey())->first();
                }

                return $this->currentReader !== null ? $this->currentReader->pivot : null;
            }
        );
    }

    protected function userReadStatus(): Attribute
    {
        return new Attribute(
            get: function ()
            {
                if ($this->isOld || !Auth::check()) {
                    return null;
                }

                if ($this->reader === null) {
                    return trans('forum::general.'.self::STATUS_UNREAD);
                }

                return $this->updatedSince($this->reader) ? trans('forum::general.'.self::STATUS_UPDATED) : null;
            }
        );
    }

    protected function postCount(): Attribute
    {
        return new Attribute(
            get: function ()
            {
                return $this->reply_count + 1;
            }
        );
    }
}
