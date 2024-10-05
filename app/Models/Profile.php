<?php

namespace App\Models;

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

    /**
     * Accessor for the gender attribute.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function gender(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['', 'Male', 'Female'][$value],
        );
    }

    /**
     * Accessor for the blood_type attribute.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function blood_type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['', 'A', 'B', 'AB', 'O'][$value],
        );
    }

    /**
     * Accessor for the identity_type attribute.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function identity_type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['', 'Passport', 'KTP'][$value],
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
}
