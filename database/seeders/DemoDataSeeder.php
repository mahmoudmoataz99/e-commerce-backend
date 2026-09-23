<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\AppNotification;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    // this creates a demo login so you can test the app right away
    // without having to register a new account every time
    // email: alex@example.com   password: password
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'alex@example.com'],
            [
                'name' => 'Alex Johnson',
                'password' => Hash::make('password'),
                'phone' => '555-123-4567',
            ]
        );

        // same 2 addresses that were hardcoded in checkout.tsx / addresses.tsx before
        Address::updateOrCreate(
            ['user_id' => $user->id, 'label' => 'Home'],
            ['address' => '123 Main St, New York, NY 10001', 'is_default' => true]
        );
        Address::updateOrCreate(
            ['user_id' => $user->id, 'label' => 'Work'],
            ['address' => '456 Office Ave, New York, NY 10002', 'is_default' => false]
        );

        // same fake cards that were hardcoded in payment-methods.tsx / checkout.tsx before
        PaymentMethod::updateOrCreate(
            ['user_id' => $user->id, 'label' => 'Visa'],
            ['last4' => '4242', 'type' => 'card', 'is_default' => true]
        );
        PaymentMethod::updateOrCreate(
            ['user_id' => $user->id, 'label' => 'Mastercard'],
            ['last4' => '8888', 'type' => 'card', 'is_default' => false]
        );

        // a couple sample notifications so the notifications screen isnt empty
        AppNotification::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Welcome to MyShop!'],
            ['body' => 'Thanks for joining. Check out our new arrivals.', 'type' => 'general']
        );
        AppNotification::updateOrCreate(
            ['user_id' => $user->id, 'title' => 'Flash Sale'],
            ['body' => 'Get 10% off with code SAVE10, today only!', 'type' => 'promo']
        );
    }
}
