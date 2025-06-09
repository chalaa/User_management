<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    //get all roles
    public function index(){
        try{
            $roles = Role::all();
            return RoleResource::collection($roles);
        }
        catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while fetching roles',
                'message' => 'An error occurred while fetching roles'
            ], 500);
        }
    }

    // get specific role by id and all permissions that belong to this role
    public function show($id){

        try{
            $role = Role::with('permissions')->findOrFail($id);
            return new RoleResource($role);
        }catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while fetching roles',
                'message' => 'An error occurred while fetching roles'
            ], 500);
        }
    }

    // Create a new role
    public function store(Request $request){
        try{
            // Validate the request
            $request->validate([
                'name' => 'required|unique:roles,name',
            ]);
    
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'api', // Assuming you are using 'api' guard
            ]);
            return new RoleResource($role);
        }
        catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while creating the role',
                'message' => 'An error occurred while creating the role'
            ], 500);
        }
    }

    // Update an existing role
    public function update(Request $request, $id){

        try{
            $request->validate([
                'name' => 'required|unique:roles,name,'.$id,
            ]);
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
            ]);
            return new RoleResource($role);
        }
        catch(\Exception $e){
            return response()->json([
            'error' => 'An error occurred while updating the role',
            'message' => 'An error occurred while updating the role'
        ], 500);
        }
    }

    // Delete a role
    public function destroy($id){
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully'], 200);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while deleting the role',
                'message' => 'An error occurred while deleting the role'
            ], 500);  
        }
    }

    // Assign a permission to a role
    public function assignPermission(Request $request, $roleId){

        try{
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name',
            ]);
            $role = Role::findOrFail($roleId);
            $role->syncPermissions($request->permissions);
            return new RoleResource($role);
        }
        catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while assigning permissions',
                'message' => 'An error occurred while assigning permissions'
            ], 500);
        }

    }
    // Revoke a permission from a role
    public function revokePermission(Request $request, $roleId){
        try {

            // Validate the request
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            // Find the role with its permissions
            $role = Role::with('permissions')->findOrFail($roleId);

            // Revoke the specified permissions
            $role->revokePermissionTo($request->permissions);

            // Reload the permissions to ensure the response reflects the updated state
            $role->load('permissions');

            return new RoleResource($role);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while revoking permissions',
                'message' => 'An error occurred while revoking permissions'
            ], 500);
        }
    }
}
