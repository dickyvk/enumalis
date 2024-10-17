<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zeus\Profile;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'activity_type',
        'activityable_id',
        'activityable_type',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function activityable()
    {
        return $this->morphTo();
    }
}
