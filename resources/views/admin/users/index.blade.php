@extends('layout.app')

@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Users</h4>

        @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('users.create'))
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                + Add User
            </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th style="width:70px;">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Direct Permissions</th>
                        <th style="width:220px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                        @php
                            $isSuperAdminUser = $user->roles->contains('slug', 'super-admin');
                        @endphp

                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                {{ $user->name }}
                                @if($isSuperAdminUser)
                                    <span class="badge bg-warning text-dark ms-1">Super Admin</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>

                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-info">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">No roles</span>
                                @endforelse
                            </td>

                            {{-- Direct Permissions --}}
                            <td>
                                @if($user->directPermissions->count())
                                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                        data-bs-target="#directPermissionsModal{{ $user->id }}">
                                        View ({{ $user->directPermissions->count() }})
                                    </button>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>

                    {{-- Actions --}}
                    <td>
                        <div class="dropdown">
                            <button class="btn p-0 dropdown-toggle hide-arrow"
                                    type="button"
                                    id="userActions{{ $user->id }}"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="userActions{{ $user->id }}">

                                {{-- Edit Access --}}
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('users.edit'))
                                    <li>
                                        <a class="dropdown-item"
                                        href="{{ route('admin.users.edit', $user) }}">
                                            <i class="bx bx-edit-alt me-2"></i>
                                            Edit Access
                                        </a>
                                    </li>
                                @endif

                                {{-- Divider --}}
                                @if(
                                    (auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('users.delete'))
                                    && !$isSuperAdminUser
                                )
                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                {{-- Delete --}}
                                @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('users.delete'))
                                    @if(!$isSuperAdminUser)
                                        <li>
                                            <form action="{{ route('admin.users.destroy', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Delete this user?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <span class="dropdown-item text-muted">
                                                🔒 Protected (Super Admin)
                                            </span>
                                        </li>
                                    @endif
                                @endif

                            </ul>
                        </div>
                    </td>

                        </tr>

                        {{-- Direct permissions modal --}}
                        @if($user->directPermissions->count())
                            <div class="modal fade" id="directPermissionsModal{{ $user->id }}" tabindex="-1"
                                aria-labelledby="directPermissionsLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="directPermissionsLabel{{ $user->id }}">
                                                Direct Permissions – {{ $user->name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            @foreach($user->directPermissions as $perm)
                                                <span class="badge bg-{{ $perm->pivot->type === 'allow' ? 'success' : 'danger' }} mb-1">
                                                    {{ $perm->slug }} ({{ $perm->pivot->type }})
                                                </span>
                                            @endforeach
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        </div>
    </div>

    {{-- Add User Modal --}}
    @if(auth()->user()->hasRole('super-admin') || auth()->user()->hasPermission('users.create'))
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Add New User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password *</label>

                                <div class="input-group">
                                    <input type="password" name="password" id="passwordInput" class="form-control" required>

                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bx bx-show"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('passwordInput');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                input.type = 'password';
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        });
    </script>
@endpush