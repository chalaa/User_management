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
        return PermissionResource::collection(Permission::all());
    }

    // get spesific permission by id and all roles that have this permission
    public function show($id)
    {   
        try {
            // Attempt to find the permission by ID
            $permission = Permission::with('roles')->findOrFail($id);
        } catch (\Exception $e) {
            // If not found, return a 404 response
            return response()->json(['message' => 'Permission not found'], 404);
        }
        return new PermissionResource($permission);
    }
}
