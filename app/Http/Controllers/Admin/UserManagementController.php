<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'staff_category' => ['required', Rule::in(['academic', 'administrative'])],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'can_manage_academic_minutes' => ['sometimes', 'boolean'],
            'can_manage_administrative_minutes' => ['sometimes', 'boolean'],
            'can_manage_proposals' => ['sometimes', 'boolean'],
            'can_manage_calendar' => ['sometimes', 'boolean'],
            'can_train_chatbot' => ['sometimes', 'boolean'],
            'can_access_settings_navigation' => ['sometimes', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->staff_category = $validated['staff_category'];
        $user->can_manage_academic_minutes = $request->boolean('can_manage_academic_minutes');
        $user->can_manage_administrative_minutes = $request->boolean('can_manage_administrative_minutes');
        $user->can_manage_proposals = $request->boolean('can_manage_proposals');
        $user->can_manage_calendar = $request->boolean('can_manage_calendar');
        $user->can_train_chatbot = $request->boolean('can_train_chatbot');
        $user->can_access_settings_navigation = $request->boolean('can_access_settings_navigation');

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 403, 'You cannot remove your own account.');

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User archived successfully.');
    }
}
