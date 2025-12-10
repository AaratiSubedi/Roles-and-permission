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
                <th></th>
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
                    <td>
                        @foreach($user->directPermissions as $perm)
                            <span class="badge bg-{{ $perm->pivot->type === 'allow' ? 'success' : 'danger' }}">
                                {{ $perm->slug }} ({{ $perm->pivot->type }})
                            </span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="btn btn-sm btn-secondary">Edit Access</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
</div>
@endsection
