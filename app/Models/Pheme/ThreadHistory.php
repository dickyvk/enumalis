<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'threads_id',
        'body',
        'edited_by',
        'edited_at',
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'threads_id');
    }
}
