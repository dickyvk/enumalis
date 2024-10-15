<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Profile extends Model
{
    use HasFactory;

    // Specify the database connection for this model
    protected $connection = 'zeus';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'users_id',
        'name',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'blood_type',
        'identity_type',
        'identity_number',
    ];

    const GENDER_OPTIONS = ['', 'Male', 'Female'];
    const BLOOD_TYPE_OPTIONS = ['', 'A', 'B', 'AB', 'O'];
    const IDENTITY_TYPE_OPTIONS = ['', 'Passport', 'KTP'];

    /**
     * Get the ID of the specified accessor attribute.
     *
     * @param string $accessor The name of the accessor to get as an ID.
     * @return string|null The ID value of the accessor, or null if not found.
     */
    public function getAccessorId(string $accessor)
    {
        // Check if the accessor exists in the attributes array
        if (array_key_exists($accessor, $this->attributes)) {
            return $this->attributes[$accessor];
        }

        // Return null if the accessor is not found
        return null;
    }


    /**
     * Accessor for the gender attribute.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function gender(): Attribute
    {
        return new Attribute(
            get: fn ($value) => self::GENDER_OPTIONS[$value],
        );
    }

    /**
     * Accessor for the blood_type attribute.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function bloodType(): Attribute
    {
        return new Attribute(
            get: fn ($value) => self::BLOOD_TYPE_OPTIONS[$value],
        );
    }

    /**
     * Accessor for the identity_type attribute.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function identityType(): Attribute
    {
        return new Attribute(
            get: fn ($value) => self::IDENTITY_TYPE_OPTIONS[$value],
        );
    }

    /**
     * Get the user that owns the profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Check if the profile is owned by the given user.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function ownedBy(User $user): bool
    {
        return $this->user->id == $user->id;
    }

    /**
     * Get the access categories IDs associated with this profile.
     *
     * @return array
     */
    public function getAccessCategoriesId()
    {
        return DB::connection('pheme')->table('forum_categories_access')->where('profiles_id', $this->id)->pluck('categories_id')->toArray();
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
