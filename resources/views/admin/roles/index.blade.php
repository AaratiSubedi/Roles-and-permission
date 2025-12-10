@extends('layout.app')

@section('title', 'Roles')

@section('content')
    <h4 class="mb-4">Roles Management</h4>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3">Create New Role</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->slug }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning">Edit</a>
                        
                        <!-- Assign Permissions Button -->
                        <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-info">Assign Permissions</a>

                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
