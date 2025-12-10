@extends('layout.app')


@section('title', 'Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Permissions</h4>
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">Add Permission</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <table class="table table-sm">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($permissions as $permission)
                <tr>
                    <td>{{ $permission->id }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->slug }}</td>
                    <td>
                        <a href="{{ route('admin.permissions.edit', $permission) }}"
                           class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.permissions.destroy', $permission) }}"
                              method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete permission?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No permissions found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
