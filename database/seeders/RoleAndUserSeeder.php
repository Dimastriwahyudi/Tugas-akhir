<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $sales      = Role::firstOrCreate(['name' => 'sales']);

        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $user->assignRole($superadmin);
    }
}