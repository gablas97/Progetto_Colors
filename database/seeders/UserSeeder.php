<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Cartoleria',
            'email' => 'admin@cartoleria.it',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Cliente 1
        $customer1 = User::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => Hash::make('password'),
            'phone' => '3331234567',
            'role' => 'customer',
            'welcome_voucher' => 5.00,
            'newsletter_subscribed' => true,
            'email_verified_at' => now(),
        ]);

        // Indirizzi Cliente 1
        Address::create([
            'user_id' => $customer1->id,
            'type' => 'both',
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'address' => 'Via Roma 123',
            'city' => 'Milano',
            'province' => 'MI',
            'postal_code' => '20100',
            'country' => 'IT',
            'phone' => '3331234567',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $customer1->id,
            'type' => 'shipping',
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'company' => 'Azienda XYZ',
            'address' => 'Via Milano 456',
            'city' => 'Roma',
            'province' => 'RM',
            'postal_code' => '00100',
            'country' => 'IT',
            'phone' => '3331234567',
            'is_default' => false,
        ]);

        // Cliente 2
        $customer2 = User::create([
            'first_name' => 'Laura',
            'last_name' => 'Bianchi',
            'email' => 'laura.bianchi@example.com',
            'password' => Hash::make('password'),
            'phone' => '3339876543',
            'role' => 'customer',
            'welcome_voucher' => 5.00,
            'newsletter_subscribed' => false,
            'email_verified_at' => now(),
        ]);

        Address::create([
            'user_id' => $customer2->id,
            'type' => 'both',
            'first_name' => 'Laura',
            'last_name' => 'Bianchi',
            'vat_number' => 'IT12345678901',
            'tax_code' => 'BNCLAR80A01H501Z',
            'address' => 'Corso Vittorio Emanuele 789',
            'city' => 'Torino',
            'province' => 'TO',
            'postal_code' => '10100',
            'country' => 'IT',
            'phone' => '3339876543',
            'is_default' => true,
        ]);

        // Cliente 3 (per test wishlist)
        $customer3 = User::create([
            'first_name' => 'Giuseppe',
            'last_name' => 'Verdi',
            'email' => 'giuseppe.verdi@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'welcome_voucher' => 5.00,
            'email_verified_at' => now(),
        ]);
    }
}