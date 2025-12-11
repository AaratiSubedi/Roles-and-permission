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
                                <div class="dropdown">
                                    <button class="btn p-0 dropdown-toggle hide-arrow" type="button"
                                        id="permissionActions{{ $permission->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="permissionActions{{ $permission->id }}">

                                        {{-- Edit --}}
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.permissions.edit', $permission->id) }}">
                                                <i class="bx bx-edit-alt me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        {{-- Delete --}}
                                        <li>
                                            <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                                method="POST" onsubmit="return confirm('Delete this permission?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>

                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection