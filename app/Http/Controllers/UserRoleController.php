<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,sales',
            'permissions' => 'array',
            'permissions.*' => 'exists:roles,id',
        ]);

        $user->role = $request->role;
        $user->save();

        // If admin, assign all permissions and disable permission changes
        if ($user->role === 'admin') {
            $allRoleIds = Role::pluck('id')->toArray();
            $user->roles()->sync($allRoleIds);
        } else {
            // Sync the selected permissions for sales user or empty array if none selected
            $permissions = $request->input('permissions', []);
            $user->roles()->sync($permissions);
        }

        return redirect()->back()->with('success', 'User role and permissions updated successfully.');
    }
}
