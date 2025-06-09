<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;  
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;


class UserController extends Controller
{
    //get all users
    public function index()
    {
        try {
            $users = User::with('roles', 'permissions')->get();
            return response()->json($users, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Users not found',
                'message' => 'Users not found'
            ], 404);
        } 
        catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching users',
                'message' => 'An error occurred while fetching users'
            ], 500);
        }
    }

    //get specific user by id
    public function show($id)
    {
        try {
            $user = User::with('roles', 'permissions')->findOrFail($id);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found',
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching user',
                'message' => 'An error occurred while fetching user'
            ], 500);
        }
    }


    //assign role to the user
    public function assignRole(Request $request)
    {
        try{
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'roles' => 'required|array|min:1',
                'roles.*' => 'string|exists:roles,name',
            ]);

            $user = User::findOrFail($request->user_id);
            $roles = $request->roles;
            $user->assignRole($role);

            return response()->json([
                'message' => 'Role assigned successfully',
                'user' => $user->load('roles')
            ], 200);

        } catch(ValidationException $e){
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'error' => 'User not found',
                'message' => 'User not found'
            ], 404);
        }  catch(RoleAlreadyExists $e){
            return response()->json([
                'error' => 'Role already assigned',
                'message' => 'Role already assigned to the user'
            ], 409);
        } catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while assigning role',
                'message' => 'An error occurred while assigning role'
            ], 500);
        }
    }

    //revoke role from the user
    public function revokeRole(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'roles' => 'required|array|min:1',
                'roles.*' => 'string|exists:roles,name',
            ]);

            $user = User::findOrFail($request->user_id);
            $roles = $request->roles;

            // Check if user has the roles
            $userRoles = $user->roles->pluck('name')->toArray();
            $missingRoles = array_diff($roles, $userRoles);

            if (!empty($missingRoles)) {
                return response()->json([
                    'error' => 'Invalid roles',
                    'message' => 'The following roles are not assigned to the user: ' . implode(', ', $missingRoles)
                ], 422);
            }

            // Remove the specified roles
            $user->removeRole(...$roles);

            return response()->json([
                'message' => 'Roles removed successfully',
                'user' => $user->load('roles')
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found',
                'message' => 'No user exists with the provided user_id'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while removing roles',
                'message' => $e->getMessage() // Remove in production
            ], 500);
        }
    }

    // assign permission to the user
    public function assignPermission(Request $request)
    {
        try{

            $request->validate([
                'user_id' => 'required|exists:users,id',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            $user = User::findOrFail($request->user_id);

            // Assign multiple permissions
            $user->givePermissionTo($request->permissions);

            return response()->json([
                'message' => 'Permissions assigned successfully',
                'user' => $user->load('permissions'),
            ], 200);

        } catch(ValidationException $e){
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'error' => 'User not found',
                'message' => 'User not found'
            ], 404);
        } catch(PermissionDoesNotExist $e){
            return response()->json([
                'error' => 'Permission not found',
                'message' => 'Permission not found'
            ], 404);
        } catch(PermissionAlreadyExists $e){
            return response()->json([
                'error' => 'Permission already assigned',
                'message' => 'Permission already assigned'
            ], 409);
        } catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while assigning permission',
                'message' => 'An error occurred while assigning permission'
            ], 500);
        }
    }

    // revoke permission from the user
    public function revokePermission(Request $request)
    {
        try{
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            $user = User::findOrFail($request->user_id);

            // Revoke multiple permissions
            $user->revokePermissionTo($request->permissions);

            return response()->json([
                'message' => 'Permissions revoked successfully',
                'user' => $user->load('permissions'),
            ], 200);
            
        } catch(ValidationException $e){
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch(ModelNotFoundException $e){
            return response()->json([
                'error' => 'User not found',
                'message' => 'User not found'
            ], 404);
        } catch(PermissionDoesNotExist $e){
            return response()->json([
                'error' => 'Permission not found',
                'message' => 'Permission not found'
            ], 404);
        } catch(PermissionAlreadyExists $e){
            return response()->json([
                'error' => 'Permission not assigned',
                'message' => 'Permission not assigned'
            ], 409);
        } catch(\Exception $e){
            return response()->json([
                'error' => 'An error occurred while revoking permission',
                'message' => 'An error occurred while revoking permission'
            ], 500);
        }
    }
}
