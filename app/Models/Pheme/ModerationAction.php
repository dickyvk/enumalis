<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zeus\Profile;

class ModerationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'threads_id',
        'post_id',
        'moderator_id',
        'action',
        'reason',
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function moderator()
    {
        return $this->belongsTo(Profile::class, 'moderator_id');
    }
}
