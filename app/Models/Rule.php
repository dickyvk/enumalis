<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $connection = 'eunomia';

    //public $timestamps = false;
    protected $primaryKey = 'users_id';

    protected $fillable = [
    	'users_id',
        'terms',
        'policy',
        'pagination',
    ];
}
