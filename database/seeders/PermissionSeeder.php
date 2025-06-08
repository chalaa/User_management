<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //this is a list of permission to be craeted for this application and if you create a new permission
        //you can add it to this list and it will be created automatically
        
        $permissions = [
            'create-roles', 'read-roles', 'update-roles', 'delete-roles',
            'create-permissions', 'read-permissions', 'update-permissions', 'delete-permissions',
            'assign-roles', 'assign-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }
    }
}
