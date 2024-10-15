<?php

namespace App\Models\Pheme;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    // Relationship with threads (many-to-many)
    public function threads()
    {
        return $this->belongsToMany(Thread::class);
    }
}
