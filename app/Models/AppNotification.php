<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Called "AppNotification" (table: app_notifications) instead of just
// "Notification" so it doesn't clash with Laravel's own built-in
// notifications system.
class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = ['user_id', 'title', 'body', 'type', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
