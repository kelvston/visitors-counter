@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <h1 class="h3 mb-4">Edit User</h1>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-4 shadow rounded bg-light">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name" type="text" class="form-control"
                       value="{{ old('name', $user->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" class="form-control"
                       value="{{ old('email', $user->email) }}" required>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="staff_category" class="form-label">Staff category</label>
                <select id="staff_category" name="staff_category" class="form-control" required>
                    <option value="academic" @selected(old('staff_category', $user->staff_category) === 'academic')>Academic staff</option>
                    <option value="administrative" @selected(old('staff_category', $user->staff_category) === 'administrative')>Administrative staff</option>
                </select>
                @error('staff_category') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <fieldset class="mb-4">
                <legend class="h6">Content management access</legend>
                <p class="form-text">Users without these permissions can view records but cannot create, edit, or remove them. Minutes permissions grant access to that category. Settings navigation visibility is separate from access to its individual tools.</p>
                <div class="form-check mb-2">
                    <input id="can_manage_academic_minutes" name="can_manage_academic_minutes" type="checkbox"
                           class="form-check-input" value="1"
                           @checked(old('can_manage_academic_minutes', $user->can_manage_academic_minutes))>
                    <label class="form-check-label" for="can_manage_academic_minutes">Manage academic meeting minutes</label>
                </div>
                <div class="form-check mb-2">
                    <input id="can_manage_administrative_minutes" name="can_manage_administrative_minutes" type="checkbox"
                           class="form-check-input" value="1"
                           @checked(old('can_manage_administrative_minutes', $user->can_manage_administrative_minutes))>
                    <label class="form-check-label" for="can_manage_administrative_minutes">Manage administrative meeting minutes</label>
                </div>
                <div class="form-check">
                    <input id="can_manage_proposals" name="can_manage_proposals" type="checkbox"
                           class="form-check-input" value="1"
                           @checked(old('can_manage_proposals', $user->can_manage_proposals))>
                    <label class="form-check-label" for="can_manage_proposals">Manage proposals</label>
                </div>
                <div class="form-check mt-2">
                    <input id="can_manage_calendar" name="can_manage_calendar" type="checkbox"
                           class="form-check-input" value="1"
                           @checked(old('can_manage_calendar', $user->can_manage_calendar))>
                    <label class="form-check-label" for="can_manage_calendar">Manage library calendar</label>
                </div>
                <div class="form-check mt-2">
                    <input id="can_train_chatbot" name="can_train_chatbot" type="checkbox"
                           class="form-check-input" value="1"
                           @checked(old('can_train_chatbot', $user->can_train_chatbot))>
                    <label class="form-check-label" for="can_train_chatbot">Train chatbot</label>
                </div>
                <div class="form-check mt-2">
                    <input id="can_access_settings_navigation" name="can_access_settings_navigation" type="checkbox"
                           class="form-check-input" value="1"
                           @checked(old('can_access_settings_navigation', $user->can_access_settings_navigation))>
                    <label class="form-check-label" for="can_access_settings_navigation">View Settings navigation</label>
                </div>
            </fieldset>

            <div class="mb-3">
                <label for="password" class="form-label">New password <span class="text-muted">(leave blank to keep current)</span></label>
                <input id="password" name="password" type="password" class="form-control" autocomplete="new-password">
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                       autocomplete="new-password">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Save changes</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
