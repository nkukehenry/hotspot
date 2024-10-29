<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Other methods...

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|in:user,admin', // Adjust roles as necessary
        ]);

        // Create a new user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password'), // Set a default password or handle it as needed
        ])->assignRole($request->role); // Assuming you are using a package like Spatie for roles

        // Redirect back with a success message
        return redirect()->route('admin.users')->with('success', 'User added successfully.');
    }
}
