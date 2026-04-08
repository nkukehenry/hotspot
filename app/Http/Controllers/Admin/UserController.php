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


    public function index(Request $request)
    {
        if (!Auth::user()->can('view_users')) {
            abort(403);
        }

        $user = Auth::user();
        $query = User::with('roles', 'company');

        // Apply Filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('company_id') && $user->hasRole('Owner')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('site_id')) {
             if ($user->hasRole('Owner')) {
                 $query->where('site_id', $request->site_id);
             } elseif ($user->hasRole('Company Admin')) {
                 $query->where('site_id', $request->site_id)
                       ->where('company_id', $user->company_id);
             }
        }

        // RBAC Scoping (if not already handled by MultitenantScope)
        if ($user->hasRole('Company Admin')) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->site_id) {
            $query->where('site_id', $user->site_id);
        }

        $users = $query->paginate(15)->withQueryString();
        
        $sites = \App\Models\Site::when($user->hasRole('Company Admin'), function($q) use ($user) {
            return $q->where('company_id', $user->company_id);
        })->get();

        $companies = $user->hasRole('Owner') ? \App\Models\Company::all() : [];

        return view('admin.users', compact('users', 'sites', 'companies'));
    }

    public function store(Request $request)
    {
        $creator = Auth::user();

        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => ['required', 'string', Rule::in(['Owner', 'Manager', 'Supervisor', 'Agent', 'Company Admin'])],
            'site_id' => 'nullable|exists:sites,id',
            'company_id' => 'nullable|exists:companies,id',
            'password' => 'nullable|string|min:8',
        ]);

        // Authorization & Assignment logic
        $siteId = null;
        $companyId = null;

        if ($creator->hasRole('Owner')) {
            $siteId = $request->site_id;
            $companyId = $request->company_id;
        } elseif ($creator->hasRole('Company Admin')) {
            $companyId = $creator->company_id;
            $siteId = $request->site_id;
            
            if ($siteId) {
                $site = \App\Models\Site::find($siteId);
                if (!$site || $site->company_id != $companyId) {
                    return redirect()->back()->with('error', 'Unauthorized site assignment.');
                }
            }

            if (in_array($request->role, ['Owner', 'Company Admin'])) {
                return redirect()->back()->with('error', 'Unauthorized role assignment.');
            }
        } elseif ($creator->hasRole('Manager') || $creator->hasRole('Supervisor')) {
            $siteId = $creator->site_id;
            $companyId = $creator->company_id;
            
            if ($request->role !== 'Agent') {
                return redirect()->back()->with('error', 'Managers and Supervisors can only create Agents.');
            }
        } else {
            return redirect()->back()->with('error', 'Unauthorized to create users.');
        }

        // Handle Password
        $password = $request->input('password') ?: \Illuminate\Support\Str::random(10);

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'site_id' => $siteId,
            'company_id' => $companyId,
            'must_change_password' => true,
        ]);
        
        $user->assignRole($request->role);

        return redirect()->route('admin.users')->with('success', "User added successfully. Temporary password: {$password}");
    }

    public function resetPassword(User $user)
    {
        if (!Auth::user()->can('edit_users')) {
            abort(403);
        }

        $creator = Auth::user();
        if ($creator->hasRole('Company Admin') && $user->company_id != $creator->company_id) {
            abort(403, 'Unauthorized company access.');
        }

        if ($creator->site_id && $user->site_id != $creator->site_id) {
            abort(403, 'Unauthorized site access.');
        }

        $newPassword = \Illuminate\Support\Str::random(10);
        
        $user->update([
            'password' => Hash::make($newPassword),
            'must_change_password' => true
        ]);

        return redirect()->back()->with('success', "Password reset successfully. New temporary password: {$newPassword}");
    }

    public function update(Request $request, User $user)
    {
        if (!Auth::user()->can('edit_users')) {
            abort(403);
        }

        $creator = Auth::user();

        // Privilege Check
        if ($creator->hasRole('Company Admin') && $user->company_id != $creator->company_id) {
            abort(403, 'Unauthorized company access.');
        }

        if ($creator->site_id && $user->site_id != $creator->site_id) {
            abort(403, 'Unauthorized site access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['nullable', 'string', Rule::in(['Owner', 'Manager', 'Supervisor', 'Agent', 'Company Admin'])],
            'site_id' => 'nullable|exists:sites,id',
            'company_id' => 'nullable|exists:companies,id',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($request->filled('role')) {
             if ($creator->hasRole('Owner')) {
                 $user->syncRoles([$request->role]);
             } elseif ($creator->hasRole('Company Admin')) {
                 if (!in_array($request->role, ['Owner', 'Company Admin'])) {
                     $user->syncRoles([$request->role]);
                 } else {
                     return redirect()->back()->with('error', 'Unauthorized role assignment.');
                 }
             } elseif (($creator->hasRole('Manager') || $creator->hasRole('Supervisor')) && $request->role === 'Agent') {
                 $user->syncRoles(['Agent']);
             } else {
                 return redirect()->back()->with('error', 'Unauthorized role assignment.');
             }
        }

        if ($creator->hasRole('Owner')) {
            if ($request->has('site_id')) $user->site_id = $request->site_id;
            if ($request->has('company_id')) $user->company_id = $request->company_id;
            $user->save();
        } elseif ($creator->hasRole('Company Admin')) {
            if ($request->has('site_id')) {
                $siteId = $request->site_id;
                if ($siteId) {
                    $site = \App\Models\Site::find($siteId);
                    if ($site && $site->company_id == $creator->company_id) {
                        $user->site_id = $siteId;
                    } else {
                        return redirect()->back()->with('error', 'Unauthorized site assignment.');
                    }
                } else {
                    $user->site_id = null;
                }
                $user->save();
            }
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!Auth::user()->can('delete_users')) {
            abort(403);
        }

        $creator = Auth::user();

        if ($creator->hasRole('Company Admin') && $user->company_id != $creator->company_id) {
            abort(403, 'Unauthorized company access.');
        }

        if ($creator->site_id && $user->site_id != $creator->site_id) {
             abort(403, 'Cannot delete user from another site.');
        }

        if ($user->id === $creator->id) {
            return redirect()->back()->with('error', 'Cannot delete yourself.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}
