<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // this is the main seeder, it just calls all the other ones in order
    // run it with: php artisan db:seed
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            PromoCodeSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
