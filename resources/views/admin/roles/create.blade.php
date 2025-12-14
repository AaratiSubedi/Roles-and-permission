@extends('layout.app')

@section('title', 'Create Role')

@section('content')
<h4 class="mb-4">Create New Role</h4>

<form action="{{ route('admin.roles.store') }}" method="POST" class="card p-3">
    @csrf

    <div class="mb-3">
        <label class="form-label">Role Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    {{-- ================= PERMISSIONS ================= --}}
    @if(isset($permissionsGrouped))
        <hr>
        <h5 class="mb-3">Assign Permissions</h5>

        <div class="d-flex gap-3 mb-3">
            <label class="form-check">
                <input type="checkbox" class="form-check-input" id="selectAllPerms">
                <span class="form-check-label">Select All</span>
            </label>

            <label class="form-check">
                <input type="checkbox" class="form-check-input" id="deselectAllPerms">
                <span class="form-check-label">Deselect All</span>
            </label>
        </div>

        <div class="row g-3">
            @foreach($permissionsGrouped as $group => $perms)
                @php $groupKey = \Str::slug($group); @endphp

                <div class="col-md-6">
                    <div class="card p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>{{ $group }}</strong>

                            <label class="form-check mb-0 small">
                                <input type="checkbox"
                                       class="form-check-input group-toggle"
                                       data-group="{{ $groupKey }}">
                                Select group
                            </label>
                        </div>

                        <div data-group-box="{{ $groupKey }}">
                            @foreach($perms as $perm)
                                <label class="form-check d-block">
                                    <input class="form-check-input perm-item"
                                           type="checkbox"
                                           name="permissions[]"
                                           value="{{ $perm->id }}">
                                    {{ $perm->name }}
                                    <small class="text-muted">({{ $perm->slug }})</small>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <button type="submit" class="btn btn-primary mt-3">Create Role</button>
</form>
@endsection

@push('scripts')
<script>
const permItems = () => document.querySelectorAll('.perm-item');

document.getElementById('selectAllPerms')?.addEventListener('change', e => {
    if (!e.target.checked) return;
    permItems().forEach(cb => cb.checked = true);
    e.target.checked = false;
});

document.getElementById('deselectAllPerms')?.addEventListener('change', e => {
    if (!e.target.checked) return;
    permItems().forEach(cb => cb.checked = false);
    e.target.checked = false;
});

document.querySelectorAll('.group-toggle').forEach(toggle => {
    toggle.addEventListener('change', e => {
        const box = document.querySelector(
            `[data-group-box="${e.target.dataset.group}"]`
        );
        box.querySelectorAll('.perm-item')
            .forEach(cb => cb.checked = e.target.checked);
    });
});
</script>
@endpush
