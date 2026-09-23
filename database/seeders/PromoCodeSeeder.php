<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    // SAVE10 was hardcoded directly in checkout.tsx before, now its a real
    // row in the database so you can add more codes from the database
    // without having to change any app code
    public function run(): void
    {
        PromoCode::updateOrCreate(
            ['code' => 'SAVE10'],
            ['discount_percent' => 10, 'is_active' => true]
        );

        PromoCode::updateOrCreate(
            ['code' => 'WELCOME20'],
            ['discount_percent' => 20, 'is_active' => true]
        );
    }
}
