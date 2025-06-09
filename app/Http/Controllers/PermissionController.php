<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //get all permissions
    public function index()
    {   
        try{
            // Fetch all permissions and return them as a collection
            return PermissionResource::collection(Permission::all());
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

        } catch(\Exception $e){
            // If an error occurs, return a 500 response with an error message
            return response()->json([
                'error' => 'An error occurred while fetching the permission',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
