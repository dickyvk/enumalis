<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $connection = 'eunomia';
    protected $primaryKey = 'users_id'; // Primary key is set to users_id

    protected $fillable = [
        'users_id',
        'terms',
        'policy',
        'pagination',
    ];

    // Optional: Automatically cast these fields to specific types
    protected $casts = [
        'terms' => 'boolean',
        'policy' => 'boolean',
        'pagination' => 'integer',
    ];

    // Relationship to User model
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
