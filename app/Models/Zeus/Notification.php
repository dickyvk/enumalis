<?php

namespace App\Models\Zeus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Import Carbon for date handling

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
        'read_at',     // Timestamp indicating when the notification was read
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
     * Mark the notification as read and store the timestamp.
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->update(['read_at' => Carbon::now()]);
    }

    /**
     * Check if the notification has been read.
     *
     * @return bool
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }
}
