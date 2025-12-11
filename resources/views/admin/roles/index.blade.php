@extends('layout.app')

@section('title', 'Roles')

@section('content')


    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-4">Roles Management</h4>

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Add Roles</a>
    </div>

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
                        <div class="dropdown">
                            <button class="btn p-0 dropdown-toggle hide-arrow" type="button" id="roleActions{{ $role->id }}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="roleActions{{ $role->id }}">

                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.roles.edit', $role->id) }}">
                                        <i class="bx bx-edit-alt me-2"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.roles.permissions', $role->id) }}">
                                        <i class="bx bx-key me-2"></i> Assign Permissions
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this role?')">
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
            @endforeach
        </tbody>
    </table>
@endsection