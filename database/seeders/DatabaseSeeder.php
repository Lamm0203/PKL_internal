<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('✅ Admin user ready: admin@example.com');

        // Customers
        User::factory(10)->create(['role' => 'customer']);
        $this->command->info('✅ 10 customer users created');

        // Categories
        $this->call(CategorySeeder::class);

        // Products
        Product::factory(50)->create();
        $this->command->info('✅ 50 products created');

        Product::factory(8)->featured()->create();
        $this->command->info('✅ 8 featured products created');

        $this->command->newLine();
        $this->command->info('🎉 Database seeding completed!');
        $this->command->info('📧 Admin login: admin@example.com / password');

        // Di dalam method run()
User::create([
    'name' => 'Admin',
    'email' => 'admin@tokoonline.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);

User::create([
    'name' => 'Customer',
    'email' => 'customer@tokoonline.com',
    'password' => Hash::make('password'),
    'role' => 'customer',
]);
    }
}
