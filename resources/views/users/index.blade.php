@extends('layouts.header')

@section('content')
    <div class=" m-4">
        <div class="breadcrumb">
        <h2 class="mb-4 text-center text-primary">Manage Users and Roles</h2>
        </div>
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{-- Assuming you have Bootstrap Icons --}}
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{-- Assuming you have Bootstrap Icons --}}
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Users Table Card --}}
        <div class="card  mb-4 breadcrumb"> {{-- Added shadow for depth --}}
            <div class="card-body p-4"> {{-- Added padding --}}
                <div class="table-responsive"> {{-- Ensures table is scrollable on small screens --}}
                    <table class="table table-bordered table-hover align-middle"> {{-- Added table-hover for interaction --}}
                        <thead class="table-dark"> {{-- Dark header for contrast --}}
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="text-center">Current Role</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $index => $user)
                            <tr>
                                <th scope="row" class="text-center">{{ $index + 1 }}</th>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                        <span class="badge rounded-pill bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }} fs-6 px-3 py-2">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                </td>
                                <td>
                                    <form action="{{ route('users.update', $user) }}" method="POST">
                                        @csrf
                                        @method('PUT') {{-- Assuming you're using PUT/PATCH for updates --}}

                                        <div class="mb-2 d-flex align-items-center">
                                            <label for="role-{{ $user->id }}" class="form-label me-2 mb-0 fw-bold text-muted">Role:</label>
                                            <select name="role" id="role-{{ $user->id }}" class="form-select form-select-sm" onchange="togglePermissions(this, {{ $user->id }})">
                                                <option value="sales" {{ $user->role == 'sales' ? 'selected' : '' }}>Sales</option>
                                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </div>

                                        {{-- Permissions Section (conditionally displayed) --}}
                                        <div id="permissions-{{ $user->id }}" class="p-3 border rounded mt-2 mb-3 bg-light" style="{{ $user->role == 'sales' ? '' : 'display:none' }}">
                                            <label class="form-label mb-2 fw-bold text-dark">Permissions:</label>
                                            <div class="row row-cols-1 row-cols-md-2 g-2"> {{-- Use grid for checkboxes --}}
                                                @foreach($roles as $role)
                                                    <div class="col">
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $role->id }}"
                                                                   id="perm-{{ $user->id }}-{{ $role->id }}"
                                                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                                                {{ $user->role == 'admin' ? 'disabled' : '' }}> {{-- Admin's permissions always disabled --}}
                                                            <label class="form-check-label" for="perm-{{ $user->id }}-{{ $role->id }}">
                                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            {{-- Message for Admin role --}}
                                            @if($user->role == 'admin')
                                                <small class="text-muted mt-2 d-block">Admin users have all permissions by default and cannot be individually deselected.</small>
                                            @endif
                                        </div>

                                        <button type="submit" class="btn btn-sm btn-primary mt-2">
                                            <i class="bi bi-save me-1"></i> Update Role
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for toggling permissions --}}
    <script>
        function togglePermissions(selectElement, userId) {
            const permDiv = document.getElementById('permissions-' + userId);
            const checkboxes = permDiv.querySelectorAll('input[type=checkbox]');
            const smallText = permDiv.querySelector('small.text-muted'); // The message for admin users

            if (selectElement.value === 'sales') {
                permDiv.style.display = ''; // Show the permissions div
                checkboxes.forEach(cb => cb.disabled = false); // Enable checkboxes
                if (smallText) smallText.style.display = 'none'; // Hide admin message
            } else { // 'admin' role selected
                permDiv.style.display = 'none'; // Hide the permissions div
                // When an admin role is selected, the permissions checkboxes are effectively irrelevant
                // but for consistency, we could visually check them if they were visible, or just disable.
                // Since we're hiding the div, just ensure they are disabled from previous state.
                checkboxes.forEach(cb => cb.disabled = true);
                if (smallText) smallText.style.display = 'block'; // Show admin message (though hidden with div)
            }
        }
    </script>
@endsection
