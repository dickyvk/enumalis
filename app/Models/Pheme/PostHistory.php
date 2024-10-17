<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'old_body',
        'new_body',
        'edited_by',
        'edited_at',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
