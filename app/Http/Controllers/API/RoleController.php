<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;



class RoleController extends Controller
{

 
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }


   
    public function store(Request $request)
    {
        $request->validate([
            'name_role' => 'required|max:50',
        ]);

        $role = Role::create([
            'name_role' => $request->name_role,
        ]);

        return response()->json([
            'status' => 'Success',
            'data' => $role,
        ], 201);
    }


    public function show(Role $role)
    {
        return response()->json($role);
    }


    
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name_role' => 'required|max:50',
        ]);

        $role->update($request->all());

        return response()->json(["status" => "Mise à jour avec succès",
            "data" => $role
        ]);
    }


    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'status' => 'Delete OK',
        ]);
    }
}