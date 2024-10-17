<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Zeus\Profile;
use App\Models\Pheme\PostHistory;
use App\Models\Pheme\Reaction;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profiles_id',
        'threads_id',
        'body',
    ];

    /**
     * Get the profile that created the post.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the thread that the post belongs to.
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Get the history of the post.
     */
    public function history()
    {
        return $this->hasMany(PostHistory::class);
    }

    /**
     * Get the reactions for the post.
     */
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function react($profileId, $reactionType)
    {
        $this->reactions()->create([
            'profiles_id' => $profileId,
            'reaction_type' => $reactionType,
        ]);
    }

    /**
     * Paginate the posts.
     */
    public function scopePaginated($query, $perPage = 10)
    {
        return $query->paginate($perPage);
    }
}
