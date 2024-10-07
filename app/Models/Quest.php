<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    protected $fillable = ['name', 'description', 'reward_points'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('completed_at');
    }
}
