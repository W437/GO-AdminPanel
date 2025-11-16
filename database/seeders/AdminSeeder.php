<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@admin.com'],
            [
                'f_name' => 'Master',
                'l_name' => 'Admin',
                'phone' => '0000000000',
                'image' => 'def.png',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
                'remember_token' => Str::random(10),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
