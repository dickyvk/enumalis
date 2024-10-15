<?php

namespace App\Models\Eunomia;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // Specify the database connection for this model
    protected $connection = 'eunomia';

    // Specify the dates for soft deletes
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'email',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'uid',
        'role',
        'remember_token',
    ];

    /**
     * Interact with the user's role.
     *
     * This attribute casts the role integer to a string representation.
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function role(): Attribute
    {
        return new Attribute(
            get: fn ($value) =>  ['user', 'master', 'admin', 'moderator'][$value],
        );
    }

    /**
     * Get all profiles associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'users_id');
    }

    /**
     * Get an array of profile IDs associated with the user.
     *
     * @return array<int>
     */
    public function getProfilesId(): array
    {
        return $this->profiles->pluck('id')->toArray();
    }

    /**
     * Get the rule associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rule(): HasOne
    {
        return $this->hasOne(Rule::class, 'users_id');
    }

    /**
     * Get the terms associated with the user's rule.
     *
     * @return bool|null
     */
    public function getTerms(): ?bool
    {
        return $this->rule->terms ?? null;
    }

    /**
     * Get the policy associated with the user's rule.
     *
     * @return bool|null
     */
    public function getPolicy(): ?bool
    {
        return $this->rule->policy ?? null;
    }

    /**
     * Get the pagination preference associated with the user's rule.
     *
     * @return int|null
     */
    public function getPaginate(): ?int
    {
        return $this->rule->pagination ?? null;
    }
}
