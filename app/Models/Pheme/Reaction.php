<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zeus\Profile;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'profiles_id',
        'reactable_id',
        'reactable_type',
        'reaction_type',
    ];

    public function reactable()
    {
        return $this->morphTo();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
