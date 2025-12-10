@extends('layout.app')

@section('title', 'Assign Permissions to Role')

@section('content')
    <h4 class="mb-4">Assign Permissions to Role: {{ $role->name }}</h4>

    <!-- Card Container for Form -->
    <div class="card shadow-sm border">
        <div class="card-header">
            <h5>Assign Permissions</h5>
        </div>
        <div class="card-body">
            <!-- Form to Assign Permissions -->
            <form action="{{ route('admin.roles.updatePermissions', $role->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="permissions" class="font-weight-bold">Permissions</label>
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="permissions[]" 
                                        value="{{ $permission->id }}"
                                        id="permission-{{ $permission->id }}" 
                                        class="form-check-input"
                                        @if($role->permissions->contains($permission->id)) checked @endif
                                    >
                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
            </form>
        </div>
        <div class="card-footer">
    <small class="text-muted">Last updated 3 mins ago</small>
</div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
