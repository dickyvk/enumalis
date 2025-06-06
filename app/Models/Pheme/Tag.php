<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function threads()
    {
        return $this->belongsToMany(Thread::class, 'threads_tags', 'tags_id', 'threads_id');
    }
}
