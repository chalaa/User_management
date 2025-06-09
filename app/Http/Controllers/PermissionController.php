<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class PermissionController extends Controller
{
    //get all permissions
    public function index()
    {   
        try{
            // Fetch all permissions and return them as a collection
            return PermissionResource::collection(Permission::all());
        }catch(ModelNotFoundException $e){
            // If no permissions are found, return a 404 response
            return response()->json([
                'error' => 'Permissions not found',
                'message' => 'No permissions available'
            ], 404);
        }
        catch(\Exception $e){
            // If an error occurs, return a 500 response with an error message
            return response()->json([
                'error' => 'An error occurred while fetching permissions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // get spesific permission by id and all roles that have this permission
    public function show($id)
    {   
        try {
            // Attempt to find the permission by ID
            $permission = Permission::with('roles')->findOrFail($id);
            return new PermissionResource($permission);

        } catch (ModelNotFoundException $e) {
            // If the permission is not found, return a 404 response
            return response()->json([
                'error' => 'Permission not found',
                'message' => 'Permission with the specified ID does not exist'
            ], 404);
        } catch (\Exception $e) {
            // If an error occurs, return a 500 response with an error message
            return response()->json([
                'error' => 'An error occurred while fetching the permission',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
