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
}
