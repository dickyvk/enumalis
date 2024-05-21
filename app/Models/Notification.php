<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $connection = 'zeus';

    protected $fillable = [
        'profiles_id',
        'title',
        'body',
        'opened',
    ];
}
