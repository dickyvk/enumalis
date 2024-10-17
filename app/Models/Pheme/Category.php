<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Zeus\Profile;
use App\Models\Pheme\Thread;
use App\Models\Pheme\Post;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    // Specify the database connection for this model
    protected $connection = 'pheme';

    protected $fillable = [
        'name',
        'description',
        'is_private',
    ];

    /**
     * Get the profiles that have access to this category.
     */
    public function accessibleProfiles()
    {
        return $this->belongsToMany(Profile::class, 'categories_access', 'categories_id', 'profiles_id');
    } 

    public function accessibleCategories()
    {
        if ($user->type === 'admin' || $user->type === 'master') {
            return self::all(); // Admins and masters can access all categories
        }

        // Normal users can access non-private categories or private categories they have access to
        return self::where('is_private', false)
            ->orWhereHas('accessibleProfiles', function ($query) use ($user) {
                $query->where('profiles_id', $user->id);
            })->get();
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
     * Get the latest thread in the category.
     */
    public function latestThread()
    {
        return $this->threads()->latest()->first(); // Get the latest thread by created_at
    }

    /**
     * Get the latest post in the category.
     */
    public function latestPost()
    {
        // Get the latest post from any thread in this category
        return Post::whereHas('thread', function ($query) {
            $query->where('categories_id', $this->id);
        })->latest()->first();
    }
}
