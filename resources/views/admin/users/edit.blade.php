@extends('layout.app')

@section('title', 'Edit User Access')

@section('content')
<h4>Edit Access: {{ $user->name }} ({{ $user->email }})</h4>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    {{-- Roles --}}
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Roles</div>
            <div class="card-body">
                <form action="{{ route('admin.users.roles.update', $user) }}" method="POST">
                    @csrf

                    @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->id }}"
                                   id="role_{{ $role->id }}"
                                   {{ in_array($role->id, $userRoleIds) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ $role->name }} ({{ $role->slug }})
                            </label>
                        </div>
                    @endforeach

                    <button class="btn btn-primary mt-3">Save Roles</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Direct permissions (Permissions not assigned to role) --}}
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Direct Permissions (Overrides)</div>
            <div class="card-body">
                <form action="{{ route('admin.users.permissions.update', $user) }}" method="POST">
                    @csrf

                    @foreach($permissionsNotAssigned as $perm)
                        @php
                            $current = $userDirectPerms[$perm->id] ?? null;
                            $currentType = $current?->pivot?->type;
                        @endphp

                        <div class="mb-2">
                            <label class="form-label d-block">
                                {{ $perm->name }} ({{ $perm->slug }})
                            </label>

                            <select name="types[{{ $perm->id }}]" class="form-select form-select-sm w-auto d-inline">
                                <option value="">inherit (from role)</option>
                                <option value="allow" {{ $currentType === 'allow' ? 'selected' : '' }}>allow</option>
                                <option value="deny"  {{ $currentType === 'deny'  ? 'selected' : '' }}>deny</option>
                            </select>
                        </div>
                    @endforeach

                    <button class="btn btn-primary mt-3">Save Direct Permissions</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
