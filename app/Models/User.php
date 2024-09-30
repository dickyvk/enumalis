<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'eunomia';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'email',
        'phone',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'uid',
        'type',
        'remember_token',
    ];

    /**
     * Interact with the user's type.
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function type(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['user', 'master', 'admin'][$value],
        );
    }
    
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'users_id');
    }

    public function getProfilesId()
    {
        return $this->profiles()->pluck('id')->toArray();
    }

    public function getTerms()
    {
        return Rule::where('users_id', $this->id)->first()->terms;
    }

    public function getPolicy()
    {
        return Rule::where('users_id', $this->id)->first()->policy;
    }
    
    public function getPaginate()
    {
        return Rule::where('users_id', $this->id)->first()->pagination;
    }
}
