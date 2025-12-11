@extends('layout.app')

@section('title', 'Users')

@section('content')
<h4>Users</h4>

<div class="card">
    <div class="card-body">
        <table class="table table-sm">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Direct Permissions</th>
                <th>Edit Access</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </td>

                    {{-- Direct Permissions column --}}
                    <td>
                        @if($user->directPermissions->count())
                            <button type="button"
                                    class="btn btn-sm btn-outline-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#directPermissionsModal{{ $user->id }}">
                                View Direct Permissions
                            </button>
                        @else
                       
                            <span class="text-muted">No direct permissions</span>
                        @endif
                    </td>

                    {{-- Edit Access --}}
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="btn btn-sm btn-primary">
                            Edit Access
                        </a>
                    </td>
                </tr>

                {{-- Modal for this user's direct permissions --}}
                @if($user->directPermissions->count())
                    <div class="modal fade"
                         id="directPermissionsModal{{ $user->id }}"
                         tabindex="-1"
                         aria-labelledby="directPermissionsLabel{{ $user->id }}"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"
                                        id="directPermissionsLabel{{ $user->id }}">
                                        Direct Permissions – {{ $user->name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    @foreach($user->directPermissions as $perm)
                                        <span class="badge bg-{{ $perm->pivot->type === 'allow' ? 'success' : 'danger' }} mb-1">
                                            {{ $perm->slug }} ({{ $perm->pivot->type }})
                                        </span>
                                    @endforeach
                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-dismiss="modal">
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
@endsection
