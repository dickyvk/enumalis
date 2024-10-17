<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zeus\Profile;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'profiles_id',
        'threads_id',
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profiles_id');
    }
}
