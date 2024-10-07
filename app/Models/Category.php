<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description'];

    // Relationship to threads
    public function threads()
    {
        return $this->hasMany(Thread::class);
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
