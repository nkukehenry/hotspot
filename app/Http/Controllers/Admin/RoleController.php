<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->hasRole('Owner')) {
            abort(403);
        }

        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('Owner')) {
            abort(403);
        }

        $permissions = Permission::all()->groupBy(function($perm) {
            $parts = explode('_', $perm->name, 2);
            return count($parts) > 1 ? $parts[1] : 'other';
        });

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Owner')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        if (!Auth::user()->hasRole('Owner')) {
            abort(403);
        }

        // Prevent editing the core Owner role
        if ($role->name === 'Owner') {
            return redirect()->route('admin.roles.index')->with('error', 'The Owner role cannot be modified.');
        }

        $permissions = Permission::all()->groupBy(function($perm) {
             $parts = explode('_', $perm->name, 2);
             return count($parts) > 1 ? $parts[1] : 'other';
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (!Auth::user()->hasRole('Owner')) {
            abort(403);
        }

        if ($role->name === 'Owner') {
             abort(403, 'Cannot modify Owner role.');
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array'
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (!Auth::user()->hasRole('Owner')) {
            abort(403);
        }

        if (in_array($role->name, ['Owner', 'Manager', 'Supervisor', 'Agent'])) {
            return redirect()->route('admin.roles.index')->with('error', 'System roles cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
