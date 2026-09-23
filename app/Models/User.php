<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// This is the User model. It represents one row in the "users" table.
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // These are the only fields we allow to be set with User::create([...])
    // This stops someone from sneaking in extra fields we don't want them to set.
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    // Never show these fields when we turn a user into JSON.
    // Very important - we do NOT want to leak the password hash to the app!
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // This tells Laravel how to convert some columns automatically.
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Below are just helper relationships so we can write things like
    // $user->orders or $user->addresses instead of writing the query by hand.

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    // Named "notifications_list" and not "notifications" because Laravel's
    // own Notifiable trait (used above) already has a notifications()
    // method for its own built-in notifications system.
    public function notifications_list()
    {
        return $this->hasMany(AppNotification::class, 'user_id');
    }
}
