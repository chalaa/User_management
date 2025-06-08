<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    //get all roles
    public function index(){
        return RoleResource::collection(Role::all());
    }

    // get specific role by id and all permissions that belong to this role
    public function show($id){
        try{
            $role = Role::with('permissions')->findOrFail($id);
        }catch(\Exception $e){
            return response()->json(['message' => 'Role not found'], 404);
        }
        return new RoleResource($role);
    }

    // Create a new role
    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api', // Assuming you are using 'api' guard
        ]);
        return new RoleResource($role);
    }

    // Update an existing role
    public function update(Request $request, $id){
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
        ]);
        try{
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
            ]);
        }catch(\Exception $e){
            return response()->json(['message' => 'Role not found'], 404);
        }
        return new RoleResource($role);
    }

    // Delete a role
    public function destroy($id){
        try {
            $role = Role::findOrFail($id);
            $role->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Role not found'], 404);  
        }
        return response()->json(['message' => 'Role deleted successfully'], 200);
    }

    // Assign a permission to a role
    public function assignPermission(Request $request, $roleId){
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try{
            $role = Role::findOrFail($roleId);
            $role->syncPermissions($request->permissions);
        }catch(\Exception $e){
            return response()->json(['message' => 'Role not found'], 404);
        }

        return new RoleResource($role);
    }
    // Revoke a permission from a role
    public function revokePermission(Request $request, $roleId){
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        try {
            // Find the role with its permissions
            $role = Role::with('permissions')->findOrFail($roleId);

            // Revoke the specified permissions
            $role->revokePermissionTo($request->permissions);

            // Reload the permissions to ensure the response reflects the updated state
            $role->load('permissions');

            return new RoleResource($role);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Role not found'], 404);
        } catch (\Exception $e) {
            // Handle other potential errors (e.g., database issues)
            return response()->json(['message' => 'An error occurred while revoking permissions'], 500);
        }
    }
}
