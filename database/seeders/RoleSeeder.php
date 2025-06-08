<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create admin role
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);

        // sync all permissions to admin role
        $adminRole->syncPermissions(Permission::all());

        // create admin user
        // Ensure the user is created only if it doesn't exist 
        // if you are using this in a production environment, you must change the email and password
        // to something secure and unique.

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('admin123'), // Use a secure password
        ]);

        // assign admin role to the admin user
        $admin->assignRole($adminRole);
    }
}
