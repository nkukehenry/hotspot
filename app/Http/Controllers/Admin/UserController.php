<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Other methods...

    public function store(Request $request)
    {
        $creator = Auth::user();

        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => ['required', 'string', Rule::in(['Owner', 'Manager', 'Supervisor', 'Agent'])],
            'site_id' => 'nullable|exists:sites,id',
        ]);

        // Authorization & Site Assignment
        $siteId = null;
        if ($creator->hasRole('Owner')) {
            $siteId = $request->site_id; // Owner can assign any site (or null for platform users)
        } else {
            // Manager/Supervisor must assign to their own site
            $siteId = $creator->site_id;
            
            // Prevent Manager/Supervisor from creating Owners or Managers (optional strictness)
            if ($request->role === 'Owner' || ($request->role === 'Manager' && !$creator->hasRole('Owner'))) { // Only Owner can create Manager? Or Manager can create Manager? Usually Manager creates Supervisor/Agent.
                 // Let's restrict: Manager -> Supervisor/Agent. Supervisor -> Agent.
                 if ($creator->hasRole('Manager') && in_array($request->role, ['Owner', 'Manager'])) {
                     return redirect()->back()->with('error', 'Managers can only create Supervisors and Agents.');
                 }
                 if ($creator->hasRole('Supervisor') && in_array($request->role, ['Owner', 'Manager', 'Supervisor'])) {
                     return redirect()->back()->with('error', 'Supervisors can only create Agents.');
                 }
            }
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'), // Set a default password or handle it as needed
            'site_id' => $siteId,
        ]);
        
        $user->assignRole($request->role);

        // Redirect back with a success message
        return redirect()->route('admin.users')->with('success', 'User added successfully.');
    }
}
