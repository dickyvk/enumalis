<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // Specify the database connection for this model
    protected $connection = 'zeus';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'profiles_id', // Foreign key referencing the profile
        'title',       // Title of the notification
        'body',        // Body content of the notification
        'opened',      // Status indicating if the notification has been opened
    ];

    /**
     * Get the profile that owns the notification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profiles_id');
    }

    /**
     * Mark the notification as opened.
     *
     * @return void
     */
    public function markAsOpened()
    {
        $this->update(['opened' => true]);
    }
}
