@extends('layout.app')


@section('title', 'Edit Role')

@section('content')
<h4>Edit Role</h4>

<form action="{{ route('admin.roles.update', $role) }}" method="POST" class="card p-3 mb-4">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ old('name', $role->name) }}">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Slug (optional)</label>
        <input name="slug" class="form-control" value="{{ old('slug', $role->slug) }}">
        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Description (optional)</label>
        <textarea name="description" class="form-control">{{ old('description', $role->description) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Permissions</label>
        <div class="row">
            @foreach($permissions as $perm)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="permissions[]"
                               id="perm_{{ $perm->id }}"
                               value="{{ $perm->id }}"
                               {{ in_array($perm->id, $rolePermissionIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                            {{ $perm->name }} ({{ $perm->slug }})
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <button class="btn btn-primary">Update</button>
</form>
@endsection
