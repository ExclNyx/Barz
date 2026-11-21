<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDemoUser(
            'admin@barz.com',
            'Admin Barz',
            '081234567890',
            'admin',
            'admin123'
        );

        $this->seedDemoUser(
            'superadmin@barz.com',
            'Super Admin Barz',
            '081234567891',
            'super_admin',
            'superadmin'
        );

        $services = [
            ['name' => 'Haircut Basic', 'description' => 'Potong rambut standar dengan teknik dasar', 'price' => 35000, 'duration' => 30],
            ['name' => 'Haircut Premium', 'description' => 'Potong rambut dengan styling modern dan konsultasi', 'price' => 75000, 'duration' => 45],
            ['name' => 'Haircut + Shave', 'description' => 'Paket lengkap potong rambut dan cukur jenggot', 'price' => 85000, 'duration' => 60],
            ['name' => 'Kids Haircut', 'description' => 'Potong rambut khusus anak-anak', 'price' => 30000, 'duration' => 25],
            ['name' => 'Beard Trim', 'description' => 'Rapikan jenggot dan kumis', 'price' => 25000, 'duration' => 20],
            ['name' => 'Hair Coloring', 'description' => 'Pewarnaan rambut dengan produk premium', 'price' => 150000, 'duration' => 90],
        ];

        foreach ($services as $service) {
            $exists = DB::table('services')->where('name', $service['name'])->exists();
            if (!$exists) {
                DB::table('services')->insert($service + [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $barbers = [
            ['name' => 'Andi', 'specialization' => 'Classic & Modern Cuts', 'experience_years' => 5],
            ['name' => 'Budi', 'specialization' => 'Fade & Undercut Specialist', 'experience_years' => 3],
            ['name' => 'Cecep', 'specialization' => 'Beard & Styling Expert', 'experience_years' => 7],
        ];

        foreach ($barbers as $barber) {
            $exists = DB::table('barbers')->where('name', $barber['name'])->exists();
            if (!$exists) {
                DB::table('barbers')->insert($barber + [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ([
            'shop_name' => 'Barz Barbershop',
            'opening_time' => '09:00',
            'closing_time' => '21:00',
            'slot_duration' => '15',
        ] as $key => $value) {
            $exists = DB::table('settings')->where('setting_key', $key)->exists();
            if (!$exists) {
                DB::table('settings')->insert([
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Create demo accounts only once. Re-running db:seed must not reset
     * an existing account's password back to a publicly documented default.
     */
    private function seedDemoUser(
        string $email,
        string $name,
        string $phone,
        string $role,
        string $initialPassword
    ): void {
        $existing = DB::table('users')->where('email', $email)->first();

        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update([
                'name' => $name,
                'phone' => $phone,
                'role' => $role,
                'updated_at' => now(),
            ]);
            return;
        }

        DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($initialPassword),
            'role' => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
