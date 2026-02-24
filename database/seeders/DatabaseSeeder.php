<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['ADMIN', 'TEACHER', 'STUDENT'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $adminEmail = env('LMS_ADMIN_EMAIL', 'admin@lms.local');
        $adminPassword = env('LMS_ADMIN_PASSWORD', 'password');

        $adminRoleId = Role::where('name', 'ADMIN')->value('id');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin',
                'password' => Hash::make($adminPassword),
                'role_id' => $adminRoleId,
            ]
        );

        // Seed library content
        $this->call(LibrarySeeder::class);
    }
}
