<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Zeus\Profile;
use App\Models\Pheme\Category;
use App\Models\Pheme\Post;
use App\Models\Pheme\Tag;
use App\Models\Pheme\ThreadHistory;
use App\Models\Pheme\Reaction;

class Thread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profiles_id',
        'categories_id',
        'title',
        'body',
        'is_pinned',
        'locked',
    ];

    /**
     * Get the profile that created the thread.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the category that owns the thread.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function history()
    {
        return $this->hasMany(ThreadHistory::class);
    }

    /**
     * Get the posts for the thread.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the tags associated with the thread.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'thread_tag');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function react($profilesId, $reactionType)
    {
        $this->reactions()->create([
            'profiles_id' => $profilesId,
            'reaction_type' => $reactionType,
        ]);
    }

    /**
     * Get the latest post date for the thread.
     */
    public function latestPostDate()
    {
        return $this->posts()->latest()->value('created_at');
    }

    /**
     * Scope for getting pinned threads.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope for getting latest threads.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the subscribed threads for a specific profile.
     */
    public function scopeSubscribed($query, $profilesId)
    {
        return $query->whereHas('subscriptions', function ($q) use ($profilesId) {
            $q->where('profiles_id', $profilesId);
        });
    }

    /**
     * Paginate the threads.
     */
    public function scopePaginated($query, $perPage = 10)
    {
        return $query->paginate($perPage);
    }
}
