<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Profile extends Model
{
    use HasFactory;

    protected $connection = 'zeus';

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

    protected function gender(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['', 'Male', 'Female'][$value],
        );
    }
    protected function blood_type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['', 'A', 'B', 'AB', 'O'][$value],
        );
    }
    protected function identity_type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['', 'Passport', 'KTP'][$value],
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function ownedBy(User $user): bool
    {
        return $this->user->id == $user->id;
    }

    public function getAccessCategoriesId()
    {
        return DB::connection('pheme')->table('forum_categories_access')->where('profiles_id', $this->id)->pluck('categories_id')->toArray();
    }
}
