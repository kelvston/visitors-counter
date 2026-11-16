@extends('layouts.header')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Users</h1>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->has('user'))
            <div class="alert alert-danger">{{ $errors->first('user') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Access</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->staff_category) }}</td>
                        <td>
                            @if ($user->isAdmin())
                                Administrator
                            @else
                                @php($permissions = collect([
                                    $user->can_manage_academic_minutes ? 'Academic minutes' : null,
                                    $user->can_manage_administrative_minutes ? 'Administrative minutes' : null,
                                    $user->can_manage_proposals ? 'Proposals' : null,
                                    $user->can_manage_calendar ? 'Library calendar' : null,
                                    $user->can_train_chatbot ? 'Train chatbot' : null,
                                    $user->can_access_settings_navigation ? 'Settings navigation' : null,
                                ])->filter())
                                {{ $permissions->isEmpty() ? 'Read only' : $permissions->implode(', ') }}
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">Edit</a>
                            @unless (auth()->user()->is($user))
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Archive this user? Their visitor records will be preserved.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
@endsection
