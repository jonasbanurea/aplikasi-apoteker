<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Owner user
        $owner = User::firstOrCreate(
            ['email' => 'owner@rotua.test'],
            [
                'name' => 'Owner Ro Tua',
                'password' => Hash::make('password'),
            ]
        );
        $owner->assignRole('owner');

        // Kasir user
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@rotua.test'],
            [
                'name' => 'Kasir Ro Tua',
                'password' => Hash::make('password'),
            ]
        );
        $kasir->assignRole('kasir');

        // Admin Gudang user
        $adminGudang = User::firstOrCreate(
            ['email' => 'gudang@rotua.test'],
            [
                'name' => 'Admin Gudang Ro Tua',
                'password' => Hash::make('password'),
            ]
        );
        $adminGudang->assignRole('admin_gudang');

        $this->command->info('Users created successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Owner: owner@rotua.test / password');
        $this->command->info('Kasir: kasir@rotua.test / password');
        $this->command->info('Admin Gudang: gudang@rotua.test / password');
    }
}
