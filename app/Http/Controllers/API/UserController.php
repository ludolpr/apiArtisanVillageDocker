<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    
    public function currentUser(Request $request)
    {
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User fetched successfully!',
            ],
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    
    public function index()
    {
        // Retrieve all users from the database
        $users = User::all();
        // Return the users as a JSON response
        return response()->json($users, 200);
    }

    
    public function show(User $user)
    {
        return response()->json($user, 200);
    }


    public function update(Request $request, User $user)
    {
        // Validate the request inputs
        $request->validate([
            'name_user' => 'required',
            'email' => 'required',
            'id_role' => 'required',
            'picture_user' => 'nullable',
        ]);
        
        $filename =  $user->picture_user;
        // Handle file upload and delete old file
        if ($request->hasFile('picture_user')) {
            if ($user->picture_user) {
                Storage::delete('public/uploads/users/' . $user->picture_user);
            }
            $filenameWithExt = $request->file('picture_user')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture_user')->getClientOriginalExtension();
            $filename = $filename . '_' . time() . '.' . $extension;
            $request->file('picture_user')->storeAs('public/uploads/users', $filename);
            $user->picture_user = $filename;
        }
        // Update user data
        $user->update([
            'name_user' => $request->name_user,
            'email' => $request->email,
            'picture_user' => $filename,
            'id_role' => $user->id_role,
        ]);

        // Return the updated user in JSON
        return response()->json([
            'status' => 'Update OK',
            'data' => $user,
        ], 200);
    }

    public function destroy(User $user)
    {
        // Delete the user picture if exists
        if ($user->picture_user) {
            Storage::delete('public/uploads/users/' . $user->picture_user);
        }

        // Delete the user
        $user->delete();

        // Return the response
        return response()->json([
            'status' => 'Delete OK',
        ], 200);
    }

   
}