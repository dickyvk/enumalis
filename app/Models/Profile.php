<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

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
}
