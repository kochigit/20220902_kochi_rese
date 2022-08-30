<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = [
            'uuid' => (String) Str::uuid(),
            'name' => 'Administrator',
            'email' => 'administrator@admin.admin',
            'email_verified_at' => date('Y-m-d H:i:s'),
            "password" => Hash::make('administrator'),
            'authority' => 'admin',
        ];
        User::create($admin);
    }
}
