<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Zeus\Profile;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    // Specify the database connection for this model
    protected $connection = 'pheme';

    protected $fillable = [
        'name',
        'description',
        'accepts_threads',
        'newest_thread_id',
        'latest_active_thread_id',
        'thread_count',
        'post_count',
        'is_private',
    ];

    /**
     * Get the profiles that have access to this category.
     */
    public function accessibleProfiles()
    {
        return $this->belongsToMany(Profile::class, 'categories_access', 'categories_id', 'profiles_id');
    }

    /**
     * Check if the current user has access to this category.
     */
    public function hasAccess($user)
    {
        // Check if the category is public or if the user is an admin or master
        if (!$this->is_private || $user->type === 'admin' || $user->type === 'master') {
            return true;
        }

        // Check if the user is in the accessible profiles
        return $this->accessibleProfiles()->where('profiles_id', $user->id)->exists();
    }

    /**
     * Get threads associated with this category.
     */
    public function threads()
    {
        return $this->hasMany(Thread::class, 'categories_id');
    }

    /**
     * Update the newest_thread_id and latest_active_thread_id.
     */
    public function updateThreadIds($threadId)
    {
        $this->newest_thread_id = $threadId; // Update to the latest thread
        $this->latest_active_thread_id = $threadId; // This can be updated to the latest active thread later if needed
        $this->save();
    }

    /**
     * Update the latest_active_thread_id.
     */
    public function updateLatestActive($threadId)
    {
        $this->latest_active_thread_id = $threadId;
        $this->save();
    }

    /**
     * Increment thread_count and post_count.
     */
    public function incrementCounts($postsCount)
    {
        $this->increment('thread_count');
        $this->increment('post_count', $postsCount); // Pass the number of posts created with the thread
    }

    /**
     * Decrement thread_count and post_count.
     */
    public function decrementCounts($postsCount)
    {
        $this->decrement('thread_count');
        $this->decrement('post_count', $postsCount);
    }
}




/*
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User;

class Category extends Model
{
    use HasFactory;

    protected $connection = 'pheme';
    protected $table = 'forum_categories';

    protected $fillable = [
        'title',
        'description',
        'accepts_threads',
        'newest_thread_id',
        'latest_active_thread_id',
        'thread_count',
        'post_count',
        'is_private',
        'color_light_mode',
        'color_dark_mode',
    ];

    public function isAccessibleTo(Profile $profile): bool
    {
        if(auth()->user() == 'master')
        {
            return true;
        }
        else
        {
            if(in_array($this->id, $profile->getAccessCategoriesId()))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class, 'categories_id');
    }

    public function newestThread(): HasOne
    {
        return $this->hasOne(Thread::class, 'id', 'newest_thread_id');
    }

    public function latestActiveThread(): HasOne
    {
        return $this->hasOne(Thread::class, 'id', 'latest_active_thread_id');
    }

    public function getNewestThreadId(): ?int
    {
        $thread = $this->threads()->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();

        return $thread ? $thread->id : null;
    }

    public function getLatestActiveThreadId(): ?int
    {
        $thread = $this->threads()->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->first();

        return $thread ? $thread->id : null;
    }

    public function getThreadCount(): int
    {
        return $this->threads()->count();
    }

    /////////////////////////////////////////////////////////////////////////////////////////

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->where('parent_id', 0);
    }

    public function scopeAcceptsThreads(Builder $query): Builder
    {
        return $query->where('accepts_threads', 1);
    }

    public function scopeIsPrivate(Builder $query): Builder
    {
        return $query->where('is_private', 1);
    }

    public function scopeThreadDestinations(Builder $query): Builder
    {
        return $query->defaultOrder()
            ->with('children')
            ->where('accepts_threads', true)
            ->withDepth();
    }

    public function isEmpty(): bool
    {
        return $this->descendants->count() == 0 && $this->threads()->withTrashed()->count() == 0;
    }

    protected function route(): Attribute
    {
        return new Attribute(
            get: fn () => Forum::route('category.show', $this),
        );
    }

    protected function styleVariables(): Attribute
    {
        return new Attribute(
            get: fn () => "--category-light: {$this->color_light_mode}; --category-dark: {$this->color_dark_mode};",
        );
    }
}
