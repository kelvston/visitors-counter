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
